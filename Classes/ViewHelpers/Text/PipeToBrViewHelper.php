<?php

declare(strict_types=1);

namespace TRAW\VhsCol\ViewHelpers\Text;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class PipeToBrViewHelper
 *
 * Simple find and replace
 *
 * Usage:
 * <vcol:text.pipeToBr text="{data.header}" />
 * <vcol:text.pipeToBr text="{data.subheader}" search="***" replace="<br/>"/>
 *
 * Inline:
 * {data.header->vcol:text.pipeToBr()->f:format.raw()}
 * {data.subheader->vcol:text.pipeToBr(search:'***',replace:'<br/>')->f:format.raw()}
 *
 * Typically you would want to wrap the result in f:format.raw
 */
class PipeToBrViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('text', 'string', 'Text to convert');
        $this->registerArgument('search', 'string', 'String that should be replaced', false, '|');
        $this->registerArgument('replace', 'string', 'Replacement', false, '<br>');
    }

    /**
     * @return string The converted String
     */
    #[\Override]
    public function render()
    {
        return str_replace(
            $this->arguments['search'],
            $this->arguments['replace'],
            $this->arguments['text'] ?? $this->renderChildren()
        );
    }
}
