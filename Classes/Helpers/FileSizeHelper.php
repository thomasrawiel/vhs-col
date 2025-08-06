<?php

declare(strict_types=1);

namespace TRAW\VhsCol\Helpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class FileSizeHelper
 */
final class FileSizeHelper
{
    /**
     * Converts a byte value into a human-readable string (e.g., "1.23 MB").
     *
     * @param int $value Value in bytes
     * @param array<string>|null $units Optional list of units (e.g. ['B','KB','MB',...]).
     *        If null, units are fetched via L10n using $translateKey and $translateExtension.
     * @param int $precision Number of decimals to include
     * @param string $decimalSeparator Character for decimal point (e.g., ",")
     * @param string $thousandsSeparator Character for thousands grouping (e.g., ".")
     * @param string $translateKey Localization key for unit labels
     * @param string $translateExtension Extension key for translation source
     * @return string Human-readable file size, e.g. "1,23 MB"
     */

    public static function convertBytesToHumanReadableFormat(
        int    $value,
        ?array $units = null,
        int    $precision = 2,
        string $decimalSeparator = ',',
        string $thousandsSeparator = '.',
        string $translateKey = 'viewhelper.format.bytes.units',
        string $translateExtension = 'fluid'
    ): string {
        $units ??= GeneralUtility::trimExplode(',', (string)LocalizationUtility::translate($translateKey, $translateExtension), true);

        if (empty($units) || (count($units) === 1 && trim($units[0]) === '')) {
            $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        }
        $value = (float)$value;

        $bytes = max($value, 0);
        $pow = $bytes > 0 ? floor(log($bytes) / log(1024)) : 0;
        $pow = min($pow, count($units) - 1);
        $bytes /= 2 ** (10 * $pow);

        return sprintf(
            '%s %s',
            number_format(
                round($bytes, 4 * $precision),
                $precision,
                $decimalSeparator,
                $thousandsSeparator
            ),
            $units[$pow]
        );
    }
}
