<?php

namespace TRAW\VhsCol\DataProcessing;

use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * Class FlexformProcessor
 */
class FlexFormProcessor implements DataProcessorInterface
{
    /**
     * @param ContentObjectRenderer $cObj
     * @param array $contentObjectConfiguration
     * @param array $processorConfiguration
     * @param array $processedData
     * @return array
     */
    public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData)
    {
        $fieldName = $cObj->stdWrapValue('fieldName', $processorConfiguration, 'pi_flexform');
        if (!isset($processedData['data'][$fieldName])) {
            return $processedData;
        }
        $originalValue = $processedData['data'][$fieldName] ?? null;
        if (!is_string($originalValue)) {
            return $processedData;
        }

        $targetVariableName = $cObj->stdWrapValue('as', $processorConfiguration, 'flexFormData');
        $flexFormData = GeneralUtility::makeInstance(FlexFormService::class)
            ->convertFlexFormContentToArray($originalValue);

        $processedData[$targetVariableName] = $flexFormData;
        return $processedData;
    }
}
