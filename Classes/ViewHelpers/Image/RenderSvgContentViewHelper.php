<?php

declare(strict_types=1);

namespace TRAW\VhsCol\ViewHelpers\Image;

use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class RenderSvgContentViewHelper
 */
class RenderSvgContentViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('svgReference', FileReference::class, 'svg Reference', true);
    }

    /**
     * @return string
     */
    #[\Override]
    public function render()
    {
        return $this->arguments['svgReference']->getContents();
    }
}
