<?php

namespace TRAW\VhsCol\Helpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class FileSizeHelper
 */
class FileSizeHelper
{

    /**
     * Convert a filesize into human readable format
     * Taken from typo3/cms-fluid BytesViewHelper
     *
     * @param int        $value     - value in bytes, e.g. filesize($file)
     * @param array|null $units     - B,KB,MB,GB,TB,PB,EB,ZB,YB
     * @param int        $precision - number of decimals
     * @param string     $decimalSeperator
     * @param string     $thousandsSeparator
     *                              if $units is null, take units from locallang.xlf of the given extension
     * @param string     $translateKey
     * @param string     $translateExtension
     *
     * @return string
     */
    public static function convertBytesToHumanReadableFormat(
        int    $value,
        ?array $units = null,
        int    $precision = 2,
        string $decimalSeperator = ',',
        string $thousandsSeparator = '.',
        string $translateKey = 'viewhelper.format.bytes.units',
        string $translateExtension = 'fluid'
    ): string
    {
        $units = $units ?? GeneralUtility::trimExplode(',', (string)LocalizationUtility::translate($translateKey, $translateExtension), true);
        if (is_numeric($value)) {
            $value = (float)$value;
        }
        if (!is_int($value) && !is_float($value)) {
            $value = 0;
        }
        $bytes = max($value, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= 2 ** (10 * $pow);

        return sprintf(
            '%s %s',
            number_format(
                round($bytes, 4 * $precision),
                $precision,
                $decimalSeperator,
                $thousandsSeparator
            ),
            $units[$pow]
        );
    }
}