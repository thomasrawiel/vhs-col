<?php

namespace TRAW\VhsCol\ViewHelpers\Link;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class ParameterPartViewHelper
 */
class ParameterPartViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        $this->registerArgument('part', 'string', 'Parameter part to be extracted', true);
        $this->registerArgument('fallback', 'string', 'Fallback text if parameter part is not set');
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $part = $this->arguments['part'] ?? null;
        if (empty($part)) {
            return '';
        }

        $extractedPart = GeneralUtility::makeInstance(\TYPO3\CMS\Core\LinkHandling\TypoLinkCodecService::class)
            ->decode($this->renderChildren())[$part] ?? null;

        return $extractedPart ?? $this->arguments['fallback'] ?? '';
    }
}
