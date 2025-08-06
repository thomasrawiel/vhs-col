<?php

declare(strict_types=1);

namespace TRAW\VhsCol\Utility;

use TRAW\VhsCol\Information\RequestType;
use TRAW\VhsCol\Information\Typo3Version;

class GeneralUtility
{
    /**
     * An addition to TYPO3s GeneralUtility
     */
    public static function underScoredToLowerCase(string $string): string
    {
        return str_replace(' ', '', str_replace('_', ' ', strtolower($string)));
    }

    /**
     * Returns ext_key as tx_extkey
     */
    public static function getTyposcriptExtensionKey(string $extKey): string
    {
        return str_starts_with($extKey, 'tx_') === false ? 'tx_' . self::underScoredToLowerCase($extKey) : strtolower($extKey);
    }

    public static function getTypo3Version(): string
    {
        return (new \TYPO3\CMS\Core\Information\Typo3Version())->getVersion();
    }

    public static function getTypo3MajorVersion(): int
    {
        return Typo3Version::getTypo3MajorVersion();
    }

    public static function isFrontend(): bool
    {
        return RequestType::isFrontend();
    }

    public static function isBackend(): bool
    {
        return RequestType::isBackend();
    }
}
