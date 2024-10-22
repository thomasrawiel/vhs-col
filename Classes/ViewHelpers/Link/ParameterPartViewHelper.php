<?php
declare(strict_types=1);
namespace TRAW\VhsCol\ViewHelpers\Link;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class ParameterPartViewHelper
 *
 * Usage:
 * Inline: {data.header_link->vcol:link.parameterPart(part:'title', fallback:'fallback text goes here')}
 *
 * In a condition, if fallback is empty or 0:
 * <f:if condition="{data.header_link->vcol:link.parameterPart(part:'title', fallback:'')}">
 * <f:then>Title is set</f:then>
 * <f:else>Title is not set</f:else>
 * </f:if>
 *
 * As a normal tag: <vcol:link.parameterPart link="{data.header_link}" part="title" fallback="fallback text goes here" />
 *
 *
 */
class ParameterPartViewHelper extends AbstractViewHelper
{
    /**
     * @var array|string[]
     */
    protected array $allowedParts = ['url', 'target', 'class', 'title', 'additionalParams'];

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('part', 'string', 'Parameter part to be extracted', true);
        $this->registerArgument('link', 'string', 'Link to parameter part');
        $this->registerArgument('fallback', 'string', 'Fallback text if parameter part is not set');
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $part = $this->arguments['part'] ?? null;
        if (empty($part) || !in_array($part, $this->allowedParts)) {
            throw new \Exception('Given parameter part is not allowed');
        }

        $extractedPart = GeneralUtility::makeInstance(\TYPO3\CMS\Core\LinkHandling\TypoLinkCodecService::class)
            ->decode($this->arguments['link'] ?? $this->renderChildren())[$part] ?? null;

        return !empty($extractedPart) ? $extractedPart : $this->arguments['fallback'] ?? '';
    }
}
