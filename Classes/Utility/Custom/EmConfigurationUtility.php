<?php

declare(strict_types=1);

namespace TRAW\VhsCol\Utility\Custom;

use TRAW\VhsCol\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class EmConfigurationUtility
 */
class EmConfigurationUtility
{
    public static function isGalleryProcessorEnabled(): bool
    {
        return (bool)ConfigurationUtility::getEmConfigurationSettings('vhs_col', 'enableGalleryProcessor');
    }

    public static function getCropVariants(): array
    {
        return GeneralUtility::trimExplode(',', ConfigurationUtility::getEmConfigurationSettings('vhs_col', 'cropVariants'), true);
    }

    public static function getDisableDefaultCropVariant(): bool {
        return (bool)ConfigurationUtility::getEmConfigurationSettings('vhs_col', 'disableDefaultCropVariant');
    }
}
