<?php
declare(strict_types=1);

namespace TRAW\VhsCol\Upgrade;

use Doctrine\DBAL\ParameterType;
use Symfony\Component\Console\Output\OutputInterface;
use TRAW\VhsCol\Configuration\CropVariants;
use TRAW\VhsCol\Event\BeforeTablesAreCheckedEvent;
use TRAW\VhsCol\Event\GetPageMediaFieldsEvent;
use TRAW\VhsCol\Event\GetTableImageFields;
use TRAW\VhsCol\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Install\Updates\ChattyInterface;
use TYPO3\CMS\Install\Updates\ConfirmableInterface;
use TYPO3\CMS\Install\Updates\Confirmation;
use TYPO3\CMS\Install\Updates\RepeatableInterface;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;
use TYPO3\CMS\Core\Upgrades\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Core\Upgrades\ReferenceIndexUpdatedPrerequisite;

#[UpgradeWizard('vhscol_cropAreaMigrate')]
final class FileReferenceCropAreas implements UpgradeWizardInterface, ConfirmableInterface, RepeatableInterface, ChattyInterface
{
    private array $checkTables = [
        'pages' => ['media'],
        'tt_content' => ['image', 'assets'],
        'tx_news_domain_model_news' => ['fal_media'],
        'tt_address' => ['image'],
    ];

    /**
     * @var OutputInterface
     */
    protected $output;

    public function __construct(
        private readonly EventDispatcher $eventDispatcher,
        private readonly ConnectionPool  $connectionPool,
    )
    {
    }

    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    /**
     * Return the speaking name of this wizard
     */
    public function getTitle(): string
    {
        return 'Move crop areas';
    }

    /**
     * Return the description for this wizard
     */
    public function getDescription(): string
    {
        return 'After activating the gallery processor, move the default crop area to desktop';
    }

    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class,
            ReferenceIndexUpdatedPrerequisite::class,
        ];
    }

    public function updateNecessary(): bool
    {
        return \TRAW\VhsCol\Utility\Custom\EmConfigurationUtility::isGalleryProcessorEnabled();
    }

    private function getIgnoreTypeBecauseDesktopCropAreaIsDisabled(): array
    {
        $isDesktopAlreadyDisabled = [];
        foreach ($this->checkTables as $table => $tableImageFields) {
            if (empty($GLOBALS['TCA'][$table]['types'])) {
                continue;
            }
            $imageFields = $this->eventDispatcher->dispatch(new GetTableImageFields($table, $tableImageFields))->getFields();
            foreach ($GLOBALS['TCA'][$table]['types'] as $typeId => $type) {
                foreach ($imageFields as $imageField) {
                    $result = $type['columnsOverrides'][$imageField]['config']['overrideChildTca']['columns']['crop']['config']['cropVariants']['desktop']['disabled'] ?? false;

                    $isDesktopAlreadyDisabled[$table][$typeId] ??= [];
                    if ($result && !in_array($imageField, $isDesktopAlreadyDisabled[$table][$typeId], true)) {
                        $isDesktopAlreadyDisabled[$table][$typeId][] = $imageField;
                    }
                }
            }
        }

        return $isDesktopAlreadyDisabled;
    }

    public function getConfirmation(): Confirmation
    {
        return new Confirmation(
            'Convert FileReference crop field',
            'Convert default cropVariant to desktop variant. This may take a while',
            false,
            'Continue? (y)',
            'Abort? (N)',
            false
        );
    }

    private function getFileReferences(array $ignoreTableFields): array
    {
        $qb = $this->connectionPool->getQueryBuilderForTable('sys_file_reference');
        $qb->select('uid', 'pid', 'uid_local', 'uid_foreign', 'tablenames', 'fieldname', 'crop')
            ->from('sys_file_reference');

        $references = $qb->executeQuery()->fetchAllAssociative();

        $filteredReferences = [];

        foreach ($references as $reference) {
            $table = $reference['tablenames'];
            $field = $reference['fieldname'];

            // Tabelle nicht in Ignore-Liste → behalten
            if (!isset($ignoreTableFields[$table])) {
                $filteredReferences[] = $reference;
                continue;
            }

            // Prüfen, ob es ein Type-Feld gibt
            $typeField = $GLOBALS['TCA'][$table]['ctrl']['type'] ?? null;
            $types = $GLOBALS['TCA'][$table]['types'] ?? [];

            // Kein Type-Feld oder nur ein Type → behalten
            if (empty($typeField) || count($types) <= 1) {
                $filteredReferences[] = $reference;
                continue;
            }

            // Typ des referenzierten Records abfragen
            $qb2 = $this->connectionPool->getQueryBuilderForTable($table);
            $qb2->getRestrictions()->removeAll();
            $row = $qb2->select($typeField)
                ->from($table)
                ->where(
                    $qb2->expr()->eq('uid', $qb2->createNamedParameter($reference['uid_foreign'], ParameterType::INTEGER))
                )
                ->executeQuery()
                ->fetchAssociative();
            $recordType = $row[$typeField] ?? null;
            // Prüfen, ob der Typ in Ignore-Tabelle vorkommt und das Feld ignoriert werden soll
            if (isset($ignoreTableFields[$table][$recordType]) && in_array($field, $ignoreTableFields[$table][$recordType] ?? [], true)) {
                // Reference ignorieren (nicht hinzufügen)
                continue;
            }

            // Ansonsten behalten
            $filteredReferences[] = $reference;
        }


        return $filteredReferences;
    }

    private function updateCrop(array $fileReference): void
    {
        if (!in_array($fileReference['fieldname'], $this->checkTables[$fileReference['tablenames']] ?? [])) {
            $this->output->writeln('<info>File reference ' . $fileReference['uid'] . ' skipped because fieldname or table not in checktables</info>');
            return;
        }

        if (empty($fileReference['crop'])) {
            $qb = $this->connectionPool->getQueryBuilderForTable('sys_file');
            $qb->getRestrictions()->removeAll();
            $fileType = $qb->select('type')
                ->from('sys_file')
                ->where(
                    $qb->expr()->eq('uid', $qb->createNamedParameter($fileReference['uid_local'], ParameterType::INTEGER))
                )
                ->executeQuery()
                ->fetchOne();

            if ($fileType !== false && (int)$fileType !== \TYPO3\CMS\Core\Resource\FileType::IMAGE) {
                $this->output->writeln('<info>File reference ' . $fileReference['uid'] . ' skipped because it\'s not an image</info>');
                return;
            }
        }

        $crop = json_decode($fileReference['crop'] ?? '', true);
        if (!is_array($crop) || empty($crop)) {
            $this->output->writeln('<info>File reference ' . $fileReference['uid'] . ' skipped because no crop</info>');
            return;
        }

        if (empty($crop['desktop'])) {
            $crop['desktop'] = $crop['default'] ?? CropVariants::$defaultCropVariant;
            unset($crop['default']);
            $fileReference['crop'] = json_encode($crop);
        }
        $qb = $this->connectionPool->getConnectionForTable('sys_file_reference');
        $result = $qb->update('sys_file_reference', $fileReference, ['uid' => $fileReference['uid']]);
        if ($result) {
            $this->output->writeln('<info>File reference ' . $fileReference['uid'] . ' updated</info>');
        } else {
            $this->output->writeln('<info>File reference ' . $fileReference['uid'] . ' no update needed</info>');
        }
    }

    public function executeUpdate(): bool
    {
        $this->checkTables = $this->eventDispatcher->dispatch(new BeforeTablesAreCheckedEvent($this->checkTables))->getTableFields();

        $ignoreTableFields = $this->getIgnoreTypeBecauseDesktopCropAreaIsDisabled();
        $references = $this->getFileReferences($ignoreTableFields);
        foreach ($references as $fileReference) {
            $this->updateCrop($fileReference);
        };
        $this->output->writeln('<info>' . count($references) . ' File references updated</info>');
        $this->output->writeln('<success>Done</success>');


        return true;
    }
}