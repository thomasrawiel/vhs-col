<?php
declare(strict_types=1);
namespace TRAW\VhsCol\ViewHelpers\Image;

use TRAW\VhsCol\Utility\SvgUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class SvgViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper implements \TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface
{
    use CompileWithRenderStatic;

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
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        return GeneralUtility::makeInstance(SvgUtility::class)->render($arguments['name'], $arguments['path'] ?? '', $arguments['file'] ?? null, $arguments['useThemePath'] ?? false);
    }

    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('file', '\TYPO3\CMS\Core\Resource\FileReference', 'FileReference', false, null);
        $this->registerArgument('name', 'string', 'FileName of SVG without extension. Either prefixable with path.', false, '');
        $this->registerArgument('path', 'string', 'Optional path to directory', false);
        $this->registerArgument('useThemePath', 'integer', 'Use the configured theme path', false, false);
    }
}
