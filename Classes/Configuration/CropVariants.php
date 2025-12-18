<?php
declare(strict_types=1);

namespace TRAW\VhsCol\Configuration;

use TRAW\VhsCol\Event\AfterAdditionalCropVariantsAddedEvent;
use TRAW\VhsCol\Event\AspectRatioSetupEvent;
use TRAW\VhsCol\Event\BeforeAdditionalCropVariantsAddedEvent;
use TRAW\VhsCol\Utility\Custom\EmConfigurationUtility;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class CropVariants
{
    public static array $defaultCropVariant = [
        'title' => 'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.crop_variant.default',
        'allowedAspectRatios' => [
            '16:9' => [
                'title' => 'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.ratio.16_9',
                'value' => 16 / 9,
            ],
            '3:2' => [
                'title' => 'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.ratio.3_2',
                'value' => 3 / 2,
            ],
            '4:3' => [
                'title' => 'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.ratio.4_3',
                'value' => 4 / 3,
            ],
            '1:1' => [
                'title' => 'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.ratio.1_1',
                'value' => 1.0,
            ],
            'NaN' => [
                'title' => 'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.ratio.free',
                'value' => 0.0,
            ],
        ],
        'selectedRatio' => 'NaN',
        'cropArea' => [
            'x' => 0.0,
            'y' => 0.0,
            'width' => 1.0,
            'height' => 1.0,
        ],
    ];

    public function __construct(
        private readonly EventDispatcher $eventDispatcher
    )
    {
    }

    public function setupCropVariants(string $_EXTKEY = 'vhs_col', string $table = 'sys_file_reference')
    {
        // Set default crop variants (desktop, tablet, mobile)
        //TYPO3\CMS\Backend\Form\Element\ImageManipulationElement::$defaultConfig
        $defaultCropVariant = $GLOBALS['TCA'][$table]['columns']['crop']['config']['cropVariants']['default']
            ?? self::$defaultCropVariant;

        $defaultCropVariant['allowedAspectRatios'] = $this->eventDispatcher->dispatch(new AspectRatioSetupEvent($defaultCropVariant['allowedAspectRatios']))->getAspectRatios();


        $GLOBALS['TCA'][$table]['columns']['crop']['config']['cropVariants']['default'] = $defaultCropVariant;

        $GLOBALS['TCA'][$table]['columns']['crop']['config']['cropVariants'] = $this->eventDispatcher->dispatch(new BeforeAdditionalCropVariantsAddedEvent($GLOBALS['TCA'][$table]['columns']['crop']['config']['cropVariants']))->getCropVariants();
        $cropVariants = \TRAW\VhsCol\Utility\Custom\EmConfigurationUtility::getCropVariants();
        foreach ($cropVariants as $variant) {
            //destructure array, keep only index 0 and 1, ignore the rest
            //desktop|16:9 => desktop and 16:9
            //fail|1:1|1:2 => fail and 1:1
            //tablet => tablet and null
            [
                $variantName,
                $variantDefault,
            ] = GeneralUtility::trimExplode('|', $variant, true)
            + [null, null];
            $GLOBALS['TCA'][$table]['columns']['crop']['config']['cropVariants'][$variantName] = $defaultCropVariant;
            $GLOBALS['TCA'][$table]['columns']['crop']['config']['cropVariants'][$variantName]['selectedRatio'] = $variantDefault ?? 'NaN';
            $GLOBALS['TCA'][$table]['columns']['crop']['config']['cropVariants'][$variantName]['title'] = 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_wizards.xlf:imwizard.crop_variant.' . $variantName;
        }

        $event = $this->eventDispatcher->dispatch(new AfterAdditionalCropVariantsAddedEvent($GLOBALS['TCA'][$table]['columns']['crop']['config']['cropVariants']));
        $GLOBALS['TCA'][$table]['columns']['crop']['config']['cropVariants'] = $event->getCropVariants();
        if ($event->hasCropVariant('default')) {
            $GLOBALS['TCA'][$table]['columns']['crop']['config']['cropVariants']['default']['disabled'] = EmConfigurationUtility::getDisableDefaultCropVariant();
        }
    }
}