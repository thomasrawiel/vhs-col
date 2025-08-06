<?php

declare(strict_types=1);

namespace TRAW\VhsCol\ViewHelpers\Media\Source;

use TRAW\VhsCol\Utility\ConfigurationUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to determine the maximum image width for <source> tags of responsive image to prevent upscaling
 */
class MaxWidthViewHelper extends AbstractViewHelper
{
    /**
     * @api
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('sources', 'array', 'Sources to check', true);
        $this->registerArgument('width', 'integer', 'Width of image');
    }

    #[\Override]
    public function render(): int
    {
        if (empty(ConfigurationUtility::getSettings('vhs_col')['picture']['sources'])) {
            throw new Exception('Gallery processor typoscript probably not loaded', 2701324351);
        }

        // If given width is empty, return first (largest) source's imageWidth
        if ($this->arguments['width'] === null) {
            return (int)(reset($this->arguments['sources'])['imageWidth']);
        }

        $imageWidth = 0;

        // Iterate through breakpoints, start with smallest
        $reversedSources = array_reverse($this->arguments['sources']);
        foreach ($reversedSources as $source) {
            $imageWidth = (int)$source['imageWidth'];
            // Stop if source's imageWidth is larger than given width
            if ($source['imageWidth'] >= $this->arguments['width']) {
                break;
            }
        }

        return (int)($imageWidth > 0 ? $imageWidth : $this->arguments['width']);
    }
}
