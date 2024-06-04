<?php

defined('TYPO3') or die('Access denied.');

call_user_func(function () {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['vcol'] = [
        'TRAW\VhsCol\ViewHelpers',
    ];

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\\CMS\\Frontend\\DataProcessing\\GalleryProcessor'] = [
        'className' => \TRAW\VhsCol\Frontend\DataProcessing\GalleryProcessor::class,
    ];
});