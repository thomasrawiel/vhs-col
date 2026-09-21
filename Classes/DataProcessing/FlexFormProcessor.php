<?php

declare(strict_types=1);

namespace TRAW\VhsCol\DataProcessing;

use TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * Class FlexformProcessor
 */
readonly class FlexFormProcessor implements DataProcessorInterface
{
    public function __construct(private FlexFormTools $flexFormTools)
    {
    }

    public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData): array
    {
        if (isset($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }

        $fieldName = $cObj->stdWrapValue('fieldName', $processorConfiguration, 'pi_flexform');
        if (!isset($processedData['data'][$fieldName])) {
            return $processedData;
        }

        $originalValue = $processedData['data'][$fieldName] ?? null;
        if (!is_string($originalValue)) {
            return $processedData;
        }

        $targetVariableName = $cObj->stdWrapValue('as', $processorConfiguration, 'flexFormData');
        $flexFormData = $this->flexFormTools
            ->convertFlexFormContentToArray($originalValue);

        $processedData[$targetVariableName] = $flexFormData;
        return $processedData;
    }
}
