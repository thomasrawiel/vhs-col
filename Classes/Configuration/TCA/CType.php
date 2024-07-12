<?php

namespace TRAW\VhsCol\Configuration\TCA;

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
     * @var string|null
     */
    protected ?string $value;
    /**
     * @var string|null
     */
    protected ?string $icon;
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
     * @param array $cType
     *
     * @throws \Exception
     */
    public function __construct(array $cType)
    {
        $this->label = $cType['label'];
        $this->value = $cType['value'];
        $this->icon = $cType['icon'] ?? null;
        $this->group = $cType['group'] ?? null;
        $this->showitem = $cType['showitem'] ?? null;
        $this->flexform = $cType['flexform'] ?? null;
        $this->columnsOverrides = $cType['columnsOverrides'] ?? null;
        $this->relativeToField = $cType['relativeToField'] ?? null;
        $this->relativePosition = $cType['relativePosition'] ?? null;

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
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string|null
     */
    public function getIcon(): string|null
    {
        return $this->icon;
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
}