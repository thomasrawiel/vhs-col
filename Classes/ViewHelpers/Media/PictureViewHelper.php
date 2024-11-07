<?php
declare(strict_types=1);

namespace TRAW\VhsCol\ViewHelpers\Media;

/*
 * This file is adapted from the FluidTYPO3/Vhs project PictureViewHelper
 */

use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

class PictureViewHelper extends AbstractTagBasedViewHelper
{
    public const SCOPE_VARIABLE_DEFAULT_SOURCE_WIDTH = 'default-source-width';
    public const SCOPE_VARIABLE_DEFAULT_SOURCE_HEIGHT = 'default-source-height';
    const SCOPE = 'TRAW\VhsCol\ViewHelpers\Media\PictureViewHelper';
    const SCOPE_VARIABLE_SRC = 'src';
    const SCOPE_VARIABLE_ID = 'treatIdAsReference';
    const SCOPE_VARIABLE_DEFAULT_SOURCE = 'default-source';

    /**
     * name of the tag to be created by this view helper
     *
     * @var string
     * @api
     */
    protected $tagName = 'picture';

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('src', 'mixed', 'Path to the image or FileReference.', true);
        $this->registerArgument(
            'treatIdAsReference',
            'boolean',
            'When TRUE treat given src argument as sys_file_reference record.',
            false,
            false
        );
        $this->registerArgument('alt', 'string', 'Text for the alt attribute.', true);
        $this->registerArgument('title', 'string', 'Text for the title attribute.');
        $this->registerArgument('class', 'string', 'CSS class(es) to set.');
        $this->registerArgument(
            'loading',
            'string',
            'Native lazy-loading for images. Can be "lazy", "eager" or "auto"'
        );
    }

    /**
     * @return string
     * @throws Exception
     */
    public function render()
    {
        $src = $this->arguments['src'];
        $treatIdAsReference = (bool)$this->arguments['treatIdAsReference'];
        if (is_object($src) && $src instanceof FileReference) {
            $src = $src->getUid();
            $treatIdAsReference = true;
        }

        $viewHelperVariableContainer = $this->renderingContext->getViewHelperVariableContainer();
        $viewHelperVariableContainer->addOrUpdate(static::SCOPE, static::SCOPE_VARIABLE_SRC, $src);
        $viewHelperVariableContainer->addOrUpdate(static::SCOPE, static::SCOPE_VARIABLE_ID, $treatIdAsReference);
        $content = $this->renderChildren();
        $viewHelperVariableContainer->remove(static::SCOPE, static::SCOPE_VARIABLE_SRC);
        $viewHelperVariableContainer->remove(static::SCOPE, static::SCOPE_VARIABLE_ID);

        if (!$viewHelperVariableContainer->exists(static::SCOPE, static::SCOPE_VARIABLE_DEFAULT_SOURCE)) {
            throw new Exception('Please add a source without a media query as a default.', 1438116616);
        }
        $defaultSource = $viewHelperVariableContainer->get(static::SCOPE, static::SCOPE_VARIABLE_DEFAULT_SOURCE);

        /** @var string $alt */
        $alt = $this->arguments['alt'];

        $defaultImage = new TagBuilder('img');
        $defaultImage->addAttribute('src', $defaultSource);
        $defaultImage->addAttribute('width', $viewHelperVariableContainer->get(static::SCOPE, static::SCOPE_VARIABLE_DEFAULT_SOURCE_WIDTH));
        $defaultImage->addAttribute('height', $viewHelperVariableContainer->get(static::SCOPE, static::SCOPE_VARIABLE_DEFAULT_SOURCE_HEIGHT));
        $defaultImage->addAttribute('alt', $alt);

        /** @var string|null $class */
        $class = $this->arguments['class'];
        if (!empty($class)) {
            $defaultImage->addAttribute('class', $class);
        }

        /** @var string|null $loading */
        $loading = $this->arguments['loading'];
        if (in_array($loading ?? '', ['lazy', 'eager', 'auto'], true)) {
            $defaultImage->addAttribute('loading', $loading);
        }

        $content .= $defaultImage->render();

        $this->tag->setContent($content);
        return $this->tag->render();
    }
}
