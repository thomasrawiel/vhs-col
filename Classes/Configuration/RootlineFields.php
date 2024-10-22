<?php
declare(strict_types=1);
namespace TRAW\VhsCol\Configuration;

use TRAW\VhsCol\Information\Typo3Version;

class RootlineFields
{
    /**
     * @param string ...$field
     */
    public static function addRootlineField(string ...$field): void
    {
        self::addRootlineFields($field);
    }

    /**
     * @param array $fields
     */
    public static function addRootlineFields(array $fields): void
    {
        if (Typo3Version::getTypo3MajorVersion() < 13 || Typo3Version::compareCurrentTypo3Version('13.2', '<')) {
            $rootlineFields = array_merge_recursive(explode(',', $GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields']), $fields);
            $GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'] = implode(',', $rootlineFields);
        }else {
            //The option $GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'] has been removed without replacement with TYPO3 13.2.
            //Relations of table pages are now always resolved with nearly no performance penalty in comparison to not having them resolved.
            //so this is obsolete with TYPO3 > 13.2
            trigger_error('addRootLineFields is obsolete, see Deprecation: #103752', E_USER_DEPRECATED);
        }
    }
}
