<?php

declare(strict_types=1);

namespace TRAW\VhsCol\Information;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Class ExtensionVersion
 */
class ExtensionVersion
{
    /**
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
