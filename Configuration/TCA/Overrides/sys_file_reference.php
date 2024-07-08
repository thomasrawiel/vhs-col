<?php
defined('TYPO3') or die ('Access denied.');

call_user_func(function($_EXTKEY = 'vhs_col', $table = 'sys_file_reference') {
    // Set default crop variants (desktop, tablet, mobile)
    $GLOBALS['TCA'][$table]['columns']['crop']['config']['cropVariants']['default'] = [
        'title' => 'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.crop_variant.default',
        'allowedAspectRatios' => [
            'NaN' => [
                'title' => 'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.ratio.free',
                'value' => 0.0,
            ],
            '1:1' => [
                'title' => 'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.ratio.1_1',
                'value' => 1,
            ],
            '4:3' => [
                'title' => 'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.ratio.4_3',
                'value' => 4 / 3,
            ],
            '16:9' => [
                'title' => 'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.ratio.16_9',
                'value' => 16 / 9,
            ],
            '32:9' => [
                'disabled' => 1,
                'title' => '32:9',
                'value' => 32 / 9,
            ]
        ],
    ];
    foreach (['desktop', 'tablet', 'mobile'] as $variant) {
        $GLOBALS['TCA'][$table]['columns']['crop']['config']['cropVariants'][$variant] = $GLOBALS['TCA'][$table]['columns']['crop']['config']['cropVariants']['default'];
        $GLOBALS['TCA'][$table]['columns']['crop']['config']['cropVariants'][$variant]['title'] = 'LLL:EXT:'.$_EXTKEY.'/Resources/Private/Language/locallang_wizards.xlf:imwizard.crop_variant.' . $variant;
    }
    $GLOBALS['TCA'][$table]['columns']['crop']['config']['cropVariants']['default']['disabled'] = 1;
});