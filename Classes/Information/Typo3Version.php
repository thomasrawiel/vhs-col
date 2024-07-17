<?php

namespace TRAW\VhsCol\Information;

class Typo3Version
{
    public static function getTypo3Version(): string
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
}