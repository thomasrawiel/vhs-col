<?php

declare(strict_types=1);

namespace TRAW\VhsCol\Configuration\TCA;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Imaging\IconFactory;
use RuntimeException;

/**
 * Represents a custom CType configuration.
 */
final class CType
{
    protected string $label;
    protected string $description;
    protected string $value;
    protected ?string $iconIdentifier;
    protected ?string $group;
    protected ?string $showitem;
    protected ?string $flexform;
    protected ?array $columnsOverrides;
    protected ?string $relativeToField;
    protected ?string $relativePosition;
    protected ?string $previewRenderer;
    protected bool $registerInNewContentElementWizard;
    protected array $defaultValues;
    protected bool $saveAndClose;

    /**
     * @param array<string, mixed> $cTypeConfiguration
     *
     * @throws RuntimeException if configuration is invalid
     */
    public function __construct(array $cTypeConfiguration)
    {
        $this->label = $cTypeConfiguration['label'] ?? '';
        $this->value = $cTypeConfiguration['value'] ?? '';

        $this->assertRequiredFields();

        $this->description = $cTypeConfiguration['description'] ?? '';
        $this->iconIdentifier = $cTypeConfiguration['icon'] ?? null;

        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $this->assertIconExists($iconFactory, $this->iconIdentifier, 'icon');

        $this->group = $cTypeConfiguration['group'] ?? 'default';
        $this->showitem = $cTypeConfiguration['showitem'] ?? null;
        $this->flexform = $cTypeConfiguration['flexform'] ?? null;
        $this->columnsOverrides = $cTypeConfiguration['columnsOverrides'] ?? null;
        $this->relativeToField = $cTypeConfiguration['relativeToField'] ?? null;
        $this->relativePosition = $cTypeConfiguration['relativePosition'] ?? null;
        $this->previewRenderer = $cTypeConfiguration['previewRenderer'] ?? null;
        $this->registerInNewContentElementWizard = (bool)($cTypeConfiguration['registerInNewContentElementWizard'] ?? true);
        $this->defaultValues = $cTypeConfiguration['defaultValues'] ?? [];
        $this->saveAndClose = (bool)($cTypeConfiguration['saveAndClose'] ?? false);
    }

    /**
     * Ensures label and value are set.
     */
    private function assertRequiredFields(): void
    {
        if ($this->label === '' || $this->value === '') {
            throw new RuntimeException('A CType must have at least a label and a value', 2787735958);
        }
    }

    /**
     * Checks whether the icon exists in IconRegistry.
     *
     * @param IconFactory $iconFactory
     * @param string|null $identifier
     * @param string      $fieldName
     */
    private function assertIconExists(IconFactory $iconFactory, ?string $identifier, string $identifierType): void
    {
        if (!empty($identifier) && $iconFactory->getIcon($identifier)->getIdentifier() === 'default-not-found') {
            throw new RuntimeException(sprintf(
                'The icon "%s", registered for CType "%s" in field "%s", does not exist. It must be registered in your Configuration/Icons.php',
                $identifier,
                $this->value,
                $identifierType
            ), 7000000000 + crc32($identifier . $identifierType));
        }
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getIconIdentifier(): ?string
    {
        return $this->iconIdentifier;
    }

    public function getGroup(): ?string
    {
        return $this->group;
    }

    public function getShowitem(): ?string
    {
        return $this->showitem;
    }

    public function getFlexform(): ?string
    {
        return $this->flexform;
    }

    public function getColumnsOverrides(): ?array
    {
        return $this->columnsOverrides;
    }

    public function getRelativeToField(): string
    {
        return $this->relativeToField ?? '';
    }

    public function getRelativePosition(): string
    {
        return $this->relativePosition ?? '';
    }

    public function getPreviewRenderer(): ?string
    {
        return $this->previewRenderer;
    }

    public function getRegisterInNewContentElementWizard(): bool
    {
        return $this->registerInNewContentElementWizard;
    }

    public function getDefaultValues(): array
    {
        return $this->defaultValues;
    }

    public function getSaveAndClose(): bool
    {
        return $this->saveAndClose;
    }

    public function __toArray(): array
    {
        return [
            'label' => $this->label,
            'value' => $this->value,
            'description' => $this->description,
            'icon' => $this->iconIdentifier,
            'group' => $this->group,
            'showitem' => $this->showitem,
            'flexform' => $this->flexform,
            'columnsOverrides' => $this->columnsOverrides,
            'relativeToField' => $this->relativeToField,
            'relativePosition' => $this->relativePosition,
            'previewRenderer' => $this->previewRenderer,
            'registerInNewContentElementWizard' => $this->registerInNewContentElementWizard,
            'defaultValues' => $this->defaultValues,
            'saveAndClose' => $this->saveAndClose,
        ];
    }
}
