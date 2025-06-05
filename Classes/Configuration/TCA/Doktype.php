<?php

namespace TRAW\VhsCol\Configuration\TCA;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Core\Imaging\IconFactory;

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

    protected ?string $additionalShowitem;

    /**
     * @var bool|mixed
     */
    protected bool $registerInDragArea;

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
        if (empty($this->label) || empty($this->value)) {
            throw new \Exception('A page type must have at least a label and a value', 5869846286);
        }
        if(!MathUtility::canBeInterpretedAsInteger($this->value)) {
            throw new \Exception('A page type must have a value that can be interpreted as integer', 6038008085);
        }
        $this->iconIdentifier = $doktypeConfiguration['icon'] ?? null;
        if (!empty($this->iconIdentifier) && (GeneralUtility::makeInstance(IconFactory::class))->getIcon($this->iconIdentifier)->getIdentifier() === 'default-not-found') {
            throw new \Exception('The icon "' . $this->iconIdentifier . '", registered for Page type "' . $this->value . '" does not exist. It must be registered in your Configuration/Icons.php', 7520653051);
        };
        $this->iconIdentifierHide = $doktypeConfiguration['icon-hide'] ?? null;
        if (!empty($this->iconIdentifierHide) && (GeneralUtility::makeInstance(IconFactory::class))->getIcon($this->iconIdentifierHide)->getIdentifier() === 'default-not-found') {
            throw new \Exception('The icon "' . $this->iconIdentifierHide . '", registered for Page type "' . $this->value . '" does not exist. It must be registered in your Configuration/Icons.php', 7101129442);
        };
        $this->iconIdentifierRoot = $doktypeConfiguration['icon-root'] ?? null;
        if (!empty($this->iconIdentifierRoot) && (GeneralUtility::makeInstance(IconFactory::class))->getIcon($this->iconIdentifierRoot)->getIdentifier() === 'default-not-found') {
            throw new \Exception('The icon "' . $this->iconIdentifierRoot . '", registered for Page type "' . $this->value . '" does not exist. It must be registered in your Configuration/Icons.php', 8669148969);
        };
        $this->iconIdentifierContentFromPid = $doktypeConfiguration['icon-contentFromPid'] ?? null;
        if (!empty($this->iconIdentifierContentFromPid) && (GeneralUtility::makeInstance(IconFactory::class))->getIcon($this->iconIdentifierContentFromPid)->getIdentifier() === 'default-not-found') {
            throw new \Exception('The icon "' . $this->iconIdentifierContentFromPid . '", registered for Page type "' . $this->value . '" does not exist. It must be registered in your Configuration/Icons.php', 8646205785);
        };
        $this->group = $doktypeConfiguration['group'] ?? 'default';
        $this->itemType = $doktypeConfiguration['itemType'] ?? \TYPO3\CMS\Core\Domain\Repository\PageRepository::DOKTYPE_DEFAULT;
        $this->columnsOverrides = $doktypeConfiguration['columnsOverrides'] ?? null;
        $this->showitem = $doktypeConfiguration['showitem'] ?? null;
        $this->additionalShowitem = $doktypeConfiguration['additionalShowitem'] ?? null;
        $this->registerInDragArea = $doktypeConfiguration['registerInDragArea'] ?? true;
        $this->allowedTables = $doktypeConfiguration['allowedTables'] ?? '*';
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

    public function getAdditionalShowitem(): ?string
    {
        return $this->additionalShowitem;
    }
}
