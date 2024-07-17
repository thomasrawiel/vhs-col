<?php

namespace TRAW\VhsCol\Information;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Class ExtensionVersion
 * @package TRAW\VhsCol\Information
 */
class ExtensionVersion
{

    /**
     * @param string $extensionName
     *
     * @return string
     * @throws \TYPO3\CMS\Core\Package\Exception
     */
    public static function getVersion(string $extensionName): string
    {
        return ExtensionManagementUtility::getExtensionVersion($extensionName);
    }

    /**
     * Get 'major version' of version, e.g., '7' from '7.3.0'
     *
     * @return int Major version, e.g., '7'
     */
    public static function getMajorVersion(string $extensionName): int
    {
        [$explodedVersion] = explode('.', self::getVersion($extensionName));
        return (int)$explodedVersion;
    }
}