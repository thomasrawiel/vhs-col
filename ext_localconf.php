<?php

defined('TYPO3') || die('Access denied.');

call_user_func(function (): void {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['vcol'] = [
        'TRAW\VhsCol\ViewHelpers',
    ];
    if (\TRAW\VhsCol\Utility\Custom\EmConfigurationUtility::isGalleryProcessorEnabled()) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Frontend\DataProcessing\GalleryProcessor::class] = [
            'className' => \TRAW\VhsCol\DataProcessing\GalleryProcessor::class,
        ];
    }
});
