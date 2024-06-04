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
        return 'tx_' . self::underScoredToLowerCase($extKey);
    }
}