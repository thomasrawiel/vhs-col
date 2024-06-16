<?php

namespace TRAW\VhsCol\DataProcessing;

use TRAW\VhsCol\Utility\ConfigurationUtility;
use TRAW\VhsCol\Utility\EmConfigurationUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class GalleryProcessor extends \TYPO3\CMS\Frontend\DataProcessing\GalleryProcessor
{
    /**
     * Get the gallery width based on vertical position
     */
    protected function determineMaximumGalleryWidth()
    {
        if(\TRAW\VhsCol\Utility\Custom\EmConfigurationUtility::isGalleryProcessorEnabled()) {
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
        }else {
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
        $CType = $recordData['CType'];
        $parentUid = $recordData['tx_container_parent'];

        if (empty($parentUid) || intval($parentUid) === 0) {
            $colPos = $recordData['colPos'];

            if (!empty($settings['CType'][$CType])) {
                return $settings['CType'][$CType][$colPos]
                    ?? $settings['CType'][$CType][$colPos]
                    ?? $settings['CType'][$CType]['default']
                    ?? $settings['CType'][$CType]['default']
                    ?? null;
            }
        } else {
            if (!empty($settings['container'])) {
                $parentGridType = $this->getParentGridType($parentUid);

                if (empty($parentGridType)) return null;
                $colPos = $recordData['colPos'];

                return $settings['container'][$parentGridType][$colPos]
                    ?? $settings['container'][$parentGridType][$colPos . '']
                    ?? $settings['container'][$parentGridType]['default']
                    ?? $settings['container'][$parentGridType]['default']
                    ?? null;
            }
        }
        return $settings['default'][$recordData['colPos']]
            ?? $settings['default']['default']
            ?? $settings['default']['default']
            ?? null;
    }

    /**
     * @param int $gridContainerUid
     *
     * @return mixed|false
     */
    protected function getParentGridType(int $gridContainerUid)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tt_content')
            ->createQueryBuilder();

        return $queryBuilder->select('CType')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq('uid', $gridContainerUid)
            )
            ->execute()
            ->fetchOne();
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
