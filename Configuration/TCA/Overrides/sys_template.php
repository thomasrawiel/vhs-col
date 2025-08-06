<?php

declare(strict_types=1);
defined('TYPO3') || die('Access denied.');
call_user_func(function ($_EXTKEY = 'vhs_col'): void {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        $_EXTKEY,
        'Configuration/TypoScript/',
        'Misc - ViewHelpers Collection'
    );
    if (\TRAW\VhsCol\Utility\Custom\EmConfigurationUtility::isGalleryProcessorEnabled()) {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
            $_EXTKEY,
            'Configuration/TypoScript/GalleryProcessor/',
            'GalleryProcessor - ViewHelpers Collection'
        );
    }
});
