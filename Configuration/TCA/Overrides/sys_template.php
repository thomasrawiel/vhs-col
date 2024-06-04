<?php
defined('TYPO3') || die('Access denied.');
call_user_func(function ($_EXTKEY = "vhscol") {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        $_EXTKEY,
        'Configuration/TypoScript/',
        'ViewHelpers Collection'
    );
});
