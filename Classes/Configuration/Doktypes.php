<?php

namespace TRAW\VhsCol\Configuration;

use TRAW\VhsCol\Configuration\TCA\Doktype;
use TYPO3\CMS\Core\DataHandling\PageDoktypeRegistry;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Doktypes
 */
final class Doktypes
{
    /**
     * Call in TCA/Overrides/pages.php
     *
     *
     * @throws \Exception
     */
    public static function registerDoktypes(array $doktypes, ?string $groupLabel = null): void
    {
        foreach ($doktypes as $doktype) {
            $d = null;
            if ($doktype instanceof Doktype || is_array($doktype) && $doktype !== []) {
                $d = is_array($doktype) ? new Doktype($doktype) : $doktype;
            }

            if (!isset($GLOBALS['TCA']['pages']['columns']['doktype']['config']['itemGroups'][$d->getGroup()])) {
                ExtensionManagementUtility::addTcaSelectItemGroup('pages', 'doktype', $d->getGroup(), $groupLabel ?? $d->getGroup());
            }

            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
                'pages',
                'doktype',
                [
                    'label' => $d->getLabel(),
                    'value' => $d->getValue(),
                    'icon' => $d->getIconIdentifier(),
                    'group' => $d->getGroup(),
                ],
            );

            if (!in_array($d->getIconIdentifier(), [null, '', '0'], true)) {
                $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes'][$d->getValue()] = $d->getIconIdentifier();
            }

            if (!in_array($d->getIconIdentifierHide(), [null, '', '0'], true)) {
                $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes'][$d->getValue() . '-hideinmenu'] = $d->getIconIdentifierHide();
            }

            if (!in_array($d->getIconIdentifierContentFromPid(), [null, '', '0'], true)) {
                $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes'][$d->getValue() . '-contentFromPid'] = $d->getIconIdentifierContentFromPid();
            }

            if (!in_array($d->getIconIdentifierRoot(), [null, '', '0'], true)) {
                $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes'][$d->getValue() . '-root'] = $d->getIconIdentifierRoot();
            }

            $showitem = $d->getShowItem() ?? $GLOBALS['TCA']['pages']['types'][(string)$d->getItemType()]['showitem'] ?? '';

            if (!in_array($d->getAdditionalShowitem(), [null, '', '0'], true)) {
                $showitem = $showitem . (str_starts_with($d->getAdditionalShowitem(), ',') ? '' : ',') . $d->getAdditionalShowitem();
            }

            $GLOBALS['TCA']['pages']['types'][(string)$d->getValue()]['showitem'] = $showitem;

            if (!in_array($d->getColumnsOverrides(), [null, []], true)) {
                $GLOBALS['TCA']['pages']['types'][(string)$d->getValue()]['columnsOverrides'] = $d->getColumnsOverrides();
            }

            $GLOBALS['TCA']['pages']['tx_vhscol_doktypes'][$d->getValue()] = $doktype;
        }
    }

    /**
     * @throws \Exception
     */
    public static function registerDoktypesAfterBootCompleted(): void
    {
        $doktypes = $GLOBALS['TCA']['pages']['tx_vhscol_doktypes'] ?? null;
        if (!empty($doktypes)) {
            $dokTypeRegistry = GeneralUtility::makeInstance(PageDoktypeRegistry::class);

            foreach ($doktypes as $doktype) {
                $d = null;
                if ($doktype instanceof Doktype || is_array($doktype) && $doktype !== []) {
                    $d = is_array($doktype) ? new Doktype($doktype) : $doktype;

                    foreach ($d->getIconIdentifiers() as $iconIdentifierType => $iconIdentifier) {
                        if (!is_null($iconIdentifier) && !self::assertIconExists($iconIdentifier, $iconIdentifierType, $d->getValue())) {
                            throw new \RuntimeException(sprintf(
                                'The icon "%s", registered for Page type "%s" in field "%s", does not exist. It must be registered in your Configuration/Icons.php',
                                $iconIdentifier,
                                $d->getValue(),
                                $iconIdentifierType
                            ), 7000000000 + crc32($iconIdentifier . $iconIdentifierType));
                        }
                    }

                    $dokTypeRegistry->add(
                        $d->getValue(),
                        [
                            'allowedTables' => $d->getAllowedTables() ?? '*',
                        ],
                    );
                }
            }
        }
    }

    private static function assertIconExists(?string $identifier): bool
    {
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);

        return $iconFactory->getIcon($identifier)->getIdentifier() === $identifier;
    }

    public static function registerDoktypesInDragArea(): ?string
    {
        $doktypes = $GLOBALS['TCA']['pages']['tx_vhscol_doktypes'] ?? null;
        if (!empty($doktypes)) {
            $registerDoktypeInTSConfig = [];
            foreach ($doktypes as $doktype) {
                $d = null;
                if ($doktype instanceof Doktype || (is_array($doktype) && $doktype !== [])) {
                    $d = is_array($doktype) ? new Doktype($doktype) : $doktype;

                    if ($d->isRegisterInDragArea() && !is_null($d->getIconIdentifier())) {
                        $registerDoktypeInTSConfig[] = $d->getValue();
                    }
                }
            }

            if (!empty($registerDoktypeInTSConfig)) {
                $doktypesString = implode(',', $registerDoktypeInTSConfig);
                return 'options.pageTree.doktypesToShowInNewPageDragArea := addToList(' . $doktypesString . ')';
            }
        }

        return null;
    }
}
