<?php

namespace TRAW\VhsCol\Utility;

class GeneralUtility
{
    /**
     * An addition to TYPO3s GeneralUtility
     *
     * @param string $string
     *
     * @return string
     */
    public static function underScoredToLowerCase(string $string): string
    {
        return str_replace(' ', '', str_replace('_', ' ', strtolower($string)));
    }

    /**
     * Returns ext_key as tx_extkey
     *
     * @param string $extKey
     *
     * @return string
     */
    public static function getTyposcriptExtensionKey(string $extKey): string
    {
        return str_starts_with($extKey, 'tx_') === false ? 'tx_' . self::underScoredToLowerCase($extKey) : strtolower($extKey);
    }

    /**
     * @return string
     */
    public static function getTypo3Version():string
    {
        return (new \TYPO3\CMS\Core\Information\Typo3Version())->getVersion();
    }

    /**
     * @return int
     */
    public static function getTypo3MajorVersion(): int
    {
        return (new \TYPO3\CMS\Core\Information\Typo3Version())->getMajorVersion();
    }

    /**
     * @return bool
     */
    public static function isFrontend():bool {
        \TYPO3\CMS\Core\Http\ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend();
    }

    /**
     * @return bool
     */
    public static function isBackend():bool {
        return \TYPO3\CMS\Core\Http\ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend();
    }
}