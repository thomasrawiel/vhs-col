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
    protected readonly ?string $label;
    /**
     * @var string|null
     */
    protected readonly ?string $value;
    /**
     * @var string|null
     */
    protected readonly ?string $icon;
    /**
     * @var string|null
     */
    protected readonly ?string $group;

    /**
     * @var string|null
     */
    protected readonly ?string $showitem;
    /**
     * @var string|null
     */
    protected readonly ?string $flexform;
    /**
     * @var array|null
     */
    protected readonly ?array $columnsOverrides;


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

        if(empty($this->label) || empty($this->value)){
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
}