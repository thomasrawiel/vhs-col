<?php

namespace TRAW\Vhscol\ViewHelpers\Link;

use TYPO3\CMS\Core\LinkHandling\TypoLinkCodecService;
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
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $part = $this->arguments['part'] ?? null;
        $typoLinkCodec = GeneralUtility::makeInstance(TypoLinkCodecService::class);
        return $typoLinkCodec->decode($this->renderChildren())[$part] ?? '';
    }
}
