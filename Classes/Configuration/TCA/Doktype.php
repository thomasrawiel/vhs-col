<?php

namespace TRAW\VhsCol\Configuration\TCA;

/**
 * Class Doktype
 */
final class Doktype
{
    /**
     * @var string|mixed|null
     */
    protected ?string $label;
    /**
     * @var string|mixed|null
     */
    protected ?string $value;
    /**
     * @var string|mixed|null
     */
    protected ?string $iconIdentifier;

    /**
     * @var string|mixed|null
     */
    protected ?string $iconIdentifierHide;
    /**
     * @var string|mixed|null
     */
    protected ?string $iconIdentifierRoot;
    /**
     * @var string|mixed|null
     */
    protected ?string $iconIdentifierContentFromPid;
    /**
     * @var string
     */
    protected string $group;

    /**
     * @var string|int|mixed|null
     */
    protected ?string $itemType;

    /**
     * @var array|mixed|null
     */
    protected ?array $columnsOverrides;
    /**
     * @var string|mixed|null
     */
    protected ?string $showitem;

    /**
     * @var bool|mixed
     */
    protected bool $registerInDragArea = true;

    /**
     * @var string|mixed|null
     */
    protected ?string $allowedTables;

    /**
     * @param array $doktypeConfiguration
     *
     * @throws \Exception
     */
    public function __construct(array $doktypeConfiguration) {
        $this->label = $doktypeConfiguration['label'] ?? null;
        $this->value = $doktypeConfiguration['value'] ?? null;
        $this->iconIdentifier = $doktypeConfiguration['icon'] ?? null;
        $this->iconIdentifierHide = $doktypeConfiguration['icon-hide'] ?? null;
        $this->iconIdentifierRoot = $doktypeConfiguration['icon-root'] ?? null;
        $this->iconIdentifierContentFromPid = $doktypeConfiguration['icon-contentFromPid'] ?? null;
        $this->group = $doktypeConfiguration['group'] ?? 'default';
        $this->itemType = $doktypeConfiguration['itemType'] ?? \TYPO3\CMS\Core\Domain\Repository\PageRepository::DOKTYPE_DEFAULT;
        $this->columnsOverrides = $doktypeConfiguration['columnsOverrides'] ?? null;
        $this->showitem = $doktypeConfiguration['showitem'] ?? null;
        $this->registerInDragArea = $doktypeConfiguration['registerInDragArea'] ?? true;
        $this->allowedTables = $doktypeConfiguration['allowedTables'] ?? '*';

        if (empty($this->label) || empty($this->value)) {
            throw new \Exception('A Doktype must have at least a label and a value');
        }
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @return string|null
     */
    public function getIconIdentifier(): ?string
    {
        return $this->iconIdentifier;
    }

    /**
     * @return string|null
     */
    public function getIconIdentifierHide(): ?string
    {
        return $this->iconIdentifierHide;
    }

    /**
     * @return string|null
     */
    public function getIconIdentifierRoot(): ?string
    {
        return $this->iconIdentifierRoot;
    }

    /**
     * @return string|null
     */
    public function getIconIdentifierContentFromPid(): ?string
    {
        return $this->iconIdentifierContentFromPid;
    }

    /**
     * @return string
     */
    public function getGroup(): string
    {
        return $this->group;
    }

    /**
     * @return string|null
     */
    public function getItemType(): ?string
    {
        return $this->itemType;
    }

    /**
     * @return array|null
     */
    public function getColumnsOverrides(): ?array
    {
        return $this->columnsOverrides;
    }

    /**
     * @return string|null
     */
    public function getShowitem(): ?string
    {
        return $this->showitem;
    }

    /**
     * @return bool
     */
    public function isRegisterInDragArea(): bool
    {
        return $this->registerInDragArea;
    }

    /**
     * @return string|null
     */
    public function getAllowedTables(): ?string
    {
        return $this->allowedTables;
    }
}
