<?php

namespace TRAW\VhsCol\ViewHelpers\Image;

use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class RenderSvgContentViewHelper
 */
class RenderSvgContentViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        $this->registerArgument('svgReference', FileReference::class, 'svg Reference', true);
    }

    /**
     * @return string
     */
    public function render()
    {
        return $this->arguments['svgReference']->getContents();
    }
}
