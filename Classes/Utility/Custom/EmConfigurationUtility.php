<?php

declare(strict_types=1);

namespace TRAW\VhsCol\Utility\Custom;

use TRAW\VhsCol\Utility\ConfigurationUtility;

/**
 * Class EmConfigurationUtility
 */
class EmConfigurationUtility
{
    public static function isGalleryProcessorEnabled(): bool
    {
        return (bool)ConfigurationUtility::getEmConfigurationSettings('vhs_col', 'enableGalleryProcessor');
    }
}
