<?php
declare(strict_types=1);

namespace TRAW\VhsCol\Configuration\TCA;

use TRAW\VhsCol\Information\Typo3Version;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class CType
 */
final class CType
{
    /**
     * @var string|null
     */
    protected ?string $label;

    /**
     * @var string
     */
    protected string $description;
    /**
     * @var string|null
     */
    protected ?string $value;
    /**
     * @var string|null
     */
    protected ?string $iconIdentifier;
    /**
     * @var string|null
     */
    protected ?string $group;

    /**
     * @var string|null
     */
    protected ?string $showitem;
    /**
     * @var string|null
     */
    protected ?string $flexform;
    /**
     * @var array|null
     */
    protected ?array $columnsOverrides;

    /**
     * @var string|mixed|null
     */
    protected ?string $relativeToField;

    /**
     * @var string|null
     */
    protected ?string $relativePosition;

    /**
     * @var string|mixed|null
     */
    protected ?string $previewRenderer;

    /**
     * @var bool|mixed
     */
    protected bool $registerInNewContentElementWizard;

    /**
     * @var array|mixed
     */
    protected array $defaultValues;

    /**
     * @var bool|mixed
     */
    protected bool $saveAndClose;


    /**
     * @param array $cTypeConfiguration
     *
     * @throws \Exception
     */
    public function __construct(array $cTypeConfiguration)
    {
        $this->label = $cTypeConfiguration['label'];
        $this->value = $cTypeConfiguration['value'];
        if (empty($this->label) || empty($this->value)) {
            throw new \Exception('A CType must have at least a label and a value');
        }

        $this->description = $cTypeConfiguration['description'] ?? '';
        $this->iconIdentifier = $cTypeConfiguration['icon'] ?? null;
        if (!empty($this->iconIdentifier) && (GeneralUtility::makeInstance(IconFactory::class))->getIcon($this->iconIdentifier)->getIdentifier() === 'default-not-found') {
            throw new \Exception('The icon "' . $this->iconIdentifier . '", registered for CType "' . $this->value . '" does not exist. It must be registered in your Configuration/Icons.php');
        };

        $this->group = $cTypeConfiguration['group'] ?? 'default';
        $this->showitem = $cTypeConfiguration['showitem'] ?? null;
        $this->flexform = $cTypeConfiguration['flexform'] ?? null;
        $this->columnsOverrides = $cTypeConfiguration['columnsOverrides'] ?? null;
        $this->relativeToField = $cTypeConfiguration['relativeToField'] ?? null;
        $this->relativePosition = $cTypeConfiguration['relativePosition'] ?? null;
        $this->previewRenderer = $cTypeConfiguration['previewRenderer'] ?? null;
        $this->registerInNewContentElementWizard = (bool)($cTypeConfiguration['registerInNewContentElementWizard'] ?? Typo3Version::getTypo3MajorVersion() > 12);
        $this->defaultValues = $cTypeConfiguration['defaultValues'] ?? [];
        $this->saveAndClose = $cTypeConfiguration['saveAndClose'] ?? false;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string|null
     */
    public function getIconIdentifier(): string|null
    {
        return $this->iconIdentifier;
    }

    /**
     * @return string|null
     */
    public function getGroup(): string|null
    {
        return $this->group;
    }

    /**
     * @return string|null
     */
    public function getShowitem(): string|null
    {
        return $this->showitem;
    }

    /**
     * @return string|null
     */
    public function getFlexform(): string|null
    {
        return $this->flexform;
    }

    /**
     * @return array|null
     */
    public function getColumnsOverrides(): array|null
    {
        return $this->columnsOverrides;
    }

    /**
     * @return string
     */
    public function getRelativeToField(): string
    {
        return $this->relativeToField ?? '';
    }

    /**
     * @return string
     */
    public function getRelativePosition(): string
    {
        return $this->relativePosition ?? '';
    }

    /**
     * @return string|null
     */
    public function getPreviewRenderer(): ?string
    {
        return $this->previewRenderer;
    }

    /**
     * @return bool
     */
    public function getRegisterInNewContentElementWizard(): bool
    {
        return $this->registerInNewContentElementWizard;
    }

    /**
     * @return array
     */
    public function getDefaultValues(): array
    {
        return $this->defaultValues;
    }

    public function getSaveAndClose(): bool
    {
        return $this->saveAndClose;
    }
}
