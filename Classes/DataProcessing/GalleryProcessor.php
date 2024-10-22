<?php
declare(strict_types=1);
namespace TRAW\VhsCol\DataProcessing;

use TRAW\VhsCol\Utility\ConfigurationUtility;
use TRAW\VhsCol\Utility\DatabaseUtility;
use TRAW\VhsCol\Utility\EmConfigurationUtility;

class GalleryProcessor extends \TYPO3\CMS\Frontend\DataProcessing\GalleryProcessor
{
    /**
     * Get the gallery width based on vertical position
     */
    protected function determineMaximumGalleryWidth()
    {
        if (\TRAW\VhsCol\Utility\Custom\EmConfigurationUtility::isGalleryProcessorEnabled()) {
            $recordData = $this->contentObjectRenderer->data;
            $settings = ConfigurationUtility::getSettings();

            if (!empty($settings['maxGalleryWidth']['default'])) {
                $recordSettings = $this->determineRecordSettings($recordData, $settings['maxGalleryWidth']);

                if (empty($recordSettings)) {
                    parent::determineMaximumGalleryWidth();
                } else {
                    if (!is_array($recordSettings)) {
                        $recordSettings = ['maxW' => $recordSettings];
                    }
                    $this->overrideMaxWidth($recordSettings);
                }
            }
        } else {
            parent::determineMaximumGalleryWidth();
        }
    }

    /**
     * @param array $recordData
     * @param array $settings
     *
     * @return mixed|null
     *
     * todo: get settings of record when inside gridelement,
     * todo: currenty gridelement settings override record type settings, which is not always so good
     */
    protected function determineRecordSettings(array $recordData, array $settings)
    {
        $CType = $recordData['CType'] ?? 'page';
        $parentUid = intval($recordData['tx_container_parent'] ?? 0);
        $colPos = $recordData['colPos'] ?? $recordData['doktype'] ?? 'default';;

        if ($parentUid === 0) {
            if (!empty($settings['CType'][$CType])) {
                return $settings['CType'][$CType][$colPos]
                    ?? $settings['CType'][$CType]['default']
                    ?? $settings['CType'][$CType]
                    ?? null;
            }
            if (!empty($settings[$CType])) {
                return $settings[$CType][$colPos]
                    ?? $settings[$CType]['default']
                    ?? $settings['CType'][$CType]
                    ?? null;
            }
        } else {
            if (!empty($settings['container'])) {
                $parentGridType = DatabaseUtility::getContentAttributeByUid($parentUid, 'CType');

                if (empty($parentGridType)) return null;
                $colPos = $recordData['colPos'];

                if (!empty($settings['container'][$parentGridType])) {
                    return $settings['container'][$parentGridType][$colPos]
                        ?? $settings['container'][$parentGridType]['default']
                        ?? $settings['container'][$parentGridType]
                        ?? null;
                }
            }
        }
        return $settings['default'][$colPos]
            ?? $settings['default']['default']
            ?? $settings['default']
            ?? null;
    }

    /**
     * @param array $recordSettings
     */
    protected function overrideMaxWidth(array $recordSettings): void
    {
        if ($this->galleryData['position']['vertical'] === 'intext') {
            $this->galleryData['width'] = $recordSettings['maxWInText'] ?? $recordSettings['maxW'] ?? $this->maxGalleryWidthInText;
        } else {
            $this->galleryData['width'] = $recordSettings['maxW'] ?? $this->maxGalleryWidth;
        }
    }
}
