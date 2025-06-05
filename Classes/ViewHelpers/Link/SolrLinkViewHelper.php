<?php
declare(strict_types=1);
namespace TRAW\VhsCol\ViewHelpers\Link;

/**
 * Class SolrLinkViewHelper
 */
class SolrLinkViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('host', 'string', '', true);
        $this->registerArgument('url', 'string', 'document url', true);
    }

    /**
     * prepend a / before the url, if the url doesn't start with a /
     *
     * @return string
     */
    public function render()
    {
        $host = $this->arguments['host'];
        $url = $this->arguments['url'];
        $glue = '';

        if (str_starts_with($url, '/') === false) {
            $glue = '/';
        }

        return $host . $glue . $url;
    }
}
