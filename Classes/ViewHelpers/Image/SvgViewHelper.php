<?php

declare(strict_types=1);

namespace TRAW\VhsCol\ViewHelpers\Image;

use TRAW\VhsCol\Utility\SvgUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class SvgViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper implements \TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface
{
    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * @param array                     $arguments
     * @param \Closure                  $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     *
     * @return string the resulting string which is directly shown
     */
    #[\Override]
    public function render()
    {
        return GeneralUtility::makeInstance(SvgUtility::class)->render((string)$this->arguments['name'], $this->arguments['path'] ?? '', $this->arguments['file'] ?? null, (bool)($this->arguments['useThemePath'] ?? false));
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument('file', \TYPO3\CMS\Core\Resource\FileReference::class, 'FileReference', false, null);
        $this->registerArgument('name', 'string', 'FileName of SVG without extension. Either prefixable with path.', false, '');
        $this->registerArgument('path', 'string', 'Optional path to directory', false);
        $this->registerArgument('useThemePath', 'integer', 'Use the configured theme path', false, false);
    }
}
