<?php

namespace TRAW\Vhscol\ViewHelpers\Text;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class PipeToBrViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        $this->registerArgument('text', 'string', 'Text to convert', true);
    }

    /**
     * @return string The converted String
     */
    public function render()
    {
        return str_replace('|', '<br>', $this->arguments['text']);
    }
}
