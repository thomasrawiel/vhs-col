<?php
declare(strict_types=1);

namespace TRAW\VhsCol\Configuration\TCA;

/**
 * Class CType
 */
final readonly class CType
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
     * @param array $cType
     *
     * @throws \Exception
     */
    public function __construct(array $cType)
    {
        $this->label = $cType['label'];
        $this->value = $cType['value'];
        $this->description = $cType['description'] ?? '';
        $this->iconIdentifier = $cType['icon'] ?? null;
        $this->group = $cType['group'] ?? null;
        $this->showitem = $cType['showitem'] ?? null;
        $this->flexform = $cType['flexform'] ?? null;
        $this->columnsOverrides = $cType['columnsOverrides'] ?? null;
        $this->relativeToField = $cType['relativeToField'] ?? null;
        $this->relativePosition = $cType['relativePosition'] ?? null;
        $this->previewRenderer = $cType['previewRenderer'] ?? null;
        $this->registerInNewContentElementWizard = $cType['registerInNewContentElementWizard'] ?? false;
        $this->defaultValues = $cType['defaultValues'] ?? [];

        if (empty($this->label) || empty($this->value)) {
            throw new \Exception('A CType must have at least a label and a value');
        }
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
}
