<?php

declare(strict_types=1);

namespace TRAW\VhsCol\Configuration\TCA;

use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * Represents a custom page doktype configuration.
 */
final class Doktype
{
    protected ?string $label;

    protected int|string|null $value;

    protected ?string $iconIdentifier;

    protected ?string $iconIdentifierHide;

    protected ?string $iconIdentifierRoot;

    protected ?string $iconIdentifierContentFromPid;

    protected string $group;

    protected ?string $itemType;

    protected ?array $columnsOverrides;

    protected ?string $showItem;

    protected ?string $additionalShowItem;

    protected bool $registerInDragArea;

    protected ?string $allowedTables;

    /**
     * @param array<string, mixed> $doktypeConfiguration
     *
     * @throws \RuntimeException if required fields or icons are invalid
     */
    public function __construct(array $doktypeConfiguration)
    {
        $this->label = $doktypeConfiguration['label'] ?? null;
        $this->value = $doktypeConfiguration['value'] ?? null;

        $this->assertRequiredFields();

        //$iconFactory = GeneralUtility::makeInstance(IconFactory::class);

        $this->iconIdentifier = $doktypeConfiguration['icon'] ?? null;
        //$this->assertIconExists($iconFactory, $this->iconIdentifier, 'icon');

        $this->iconIdentifierHide = $doktypeConfiguration['icon-hide'] ?? null;
        //$this->assertIconExists($iconFactory, $this->iconIdentifierHide, 'icon-hide');

        $this->iconIdentifierRoot = $doktypeConfiguration['icon-root'] ?? null;
        //$this->assertIconExists($iconFactory, $this->iconIdentifierRoot, 'icon-root');

        $this->iconIdentifierContentFromPid = $doktypeConfiguration['icon-contentFromPid'] ?? null;
        //$this->assertIconExists($iconFactory, $this->iconIdentifierContentFromPid, 'icon-contentFromPid');

        $this->group = $doktypeConfiguration['group'] ?? 'default';
        $this->itemType = (string)($doktypeConfiguration['itemType'] ?? PageRepository::DOKTYPE_DEFAULT);
        $this->columnsOverrides = $doktypeConfiguration['columnsOverrides'] ?? null;
        $this->showItem = $doktypeConfiguration['showItem'] ?? null;
        $this->additionalShowItem = $doktypeConfiguration['additionalShowItem'] ?? null;
        $this->registerInDragArea = $doktypeConfiguration['registerInDragArea'] ?? true;
        $this->allowedTables = $doktypeConfiguration['allowedTables'] ?? '*';
    }

    /**
     * @throws \RuntimeException if label or value are missing or invalid
     */
    private function assertRequiredFields(): void
    {
        if ($this->label === null || $this->label === '' || $this->label === '0' || ($this->value === 0 || ($this->value === '' || $this->value === '0') || $this->value === null)) {
            throw new \RuntimeException('A page type must have at least a label and a value', 5869846286);
        }

        if (!MathUtility::canBeInterpretedAsInteger($this->value)) {
            throw new \RuntimeException('Page type value must be an integer or integer string', 6038008085);
        }
    }

    /**
     * @throws \RuntimeException if the icon is set but not found
     */
    private function assertIconExists(IconFactory $iconFactory, ?string $identifier, string $identifierType): void
    {
        if ($identifier !== null && $identifier !== '' && $identifier !== '0' && $iconFactory->getIcon($identifier)->getIdentifier() === 'default-not-found') {
            throw new \RuntimeException(sprintf(
                'The icon "%s", registered for Page type "%s" in field "%s", does not exist. It must be registered in your Configuration/Icons.php',
                $identifier,
                $this->value,
                $identifierType
            ), 7000000000 + crc32($identifier . $identifierType));
        }
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getValue(): int|string|null
    {
        return $this->value;
    }

    public function getIconIdentifier(): ?string
    {
        return $this->iconIdentifier;
    }

    public function getIconIdentifierHide(): ?string
    {
        return $this->iconIdentifierHide;
    }

    public function getIconIdentifierRoot(): ?string
    {
        return $this->iconIdentifierRoot;
    }

    public function getIconIdentifierContentFromPid(): ?string
    {
        return $this->iconIdentifierContentFromPid;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function getItemType(): ?string
    {
        return $this->itemType;
    }

    public function getColumnsOverrides(): ?array
    {
        return $this->columnsOverrides;
    }

    public function getShowItem(): ?string
    {
        return $this->showItem;
    }

    public function getAdditionalShowItem(): ?string
    {
        return $this->additionalShowItem;
    }

    public function isRegisterInDragArea(): bool
    {
        return $this->registerInDragArea;
    }

    public function getAllowedTables(): ?string
    {
        return $this->allowedTables;
    }
}
