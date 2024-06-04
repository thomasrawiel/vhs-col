<?php

namespace TRAW\VhsCol\ViewHelpers\Media;

use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

class PictureViewHelper extends \FluidTYPO3\Vhs\ViewHelpers\Media\PictureViewHelper
{
    public const SCOPE_VARIABLE_DEFAULT_SOURCE_WIDTH = 'default-source-width';
    public const SCOPE_VARIABLE_DEFAULT_SOURCE_HEIGHT = 'default-source-height';

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

        $this->renderingContext->getViewHelperVariableContainer()->addOrUpdate(static::SCOPE, static::SCOPE_VARIABLE_SRC, $src);
        $this->renderingContext->getViewHelperVariableContainer()->addOrUpdate(static::SCOPE, static::SCOPE_VARIABLE_ID, $treatIdAsReference);
        $content = $this->renderChildren();
        $this->renderingContext->getViewHelperVariableContainer()->remove(static::SCOPE, static::SCOPE_VARIABLE_SRC);
        $this->renderingContext->getViewHelperVariableContainer()->remove(static::SCOPE, static::SCOPE_VARIABLE_ID);

        if ($this->renderingContext->getViewHelperVariableContainer()->exists(static::SCOPE, static::SCOPE_VARIABLE_DEFAULT_SOURCE) === false) {
            throw new Exception('Please add a source without a media query as a default.', 1438116616);
        }
        $defaultSource = $this->renderingContext->getViewHelperVariableContainer()->get(static::SCOPE, static::SCOPE_VARIABLE_DEFAULT_SOURCE);

        $defaultImage = new TagBuilder('img');
        $defaultImage->addAttribute('src', $defaultSource);
        $defaultImage->addAttribute('width', $this->renderingContext->getViewHelperVariableContainer()->get(static::SCOPE, static::SCOPE_VARIABLE_DEFAULT_SOURCE_WIDTH));
        $defaultImage->addAttribute('height', $this->renderingContext->getViewHelperVariableContainer()->get(static::SCOPE, static::SCOPE_VARIABLE_DEFAULT_SOURCE_HEIGHT));
        $defaultImage->addAttribute('alt', $this->arguments['alt']);

        if (empty($this->arguments['class']) === false) {
            $defaultImage->addAttribute('class', $this->arguments['class']);
        }

        if (empty($this->arguments['title']) === false) {
            $defaultImage->addAttribute('title', $this->arguments['title']);
        }

        if (in_array($this->arguments['loading'] ?? '', ['lazy', 'eager', 'auto'], true)) {
            $defaultImage->addAttribute('loading', $this->arguments['loading']);
        }

        $content .= $defaultImage->render();

        $this->tag->setContent($content);
        return $this->tag->render();
    }
}
