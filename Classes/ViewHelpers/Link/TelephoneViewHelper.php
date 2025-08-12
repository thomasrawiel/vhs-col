<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace TRAW\VhsCol\ViewHelpers\Link;

use libphonenumber\PhoneNumberFormat;
use Psr\Http\Message\ServerRequestInterface;
use TRAW\VhsCol\Utility\PhoneNumberUtility;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Typolink\LinkFactory;
use TYPO3\CMS\Frontend\Typolink\UnableToLinkException;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Class TelephoneViewHelper
 */
final class TelephoneViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'a';

    
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('tel', 'string', 'The telephone number to be turned into a link', true);

    }

    
    public function render(): string
    {
        $tel = $this->arguments['tel'];
        $linkHref = PhoneNumberUtility::parsePhoneNumber($tel, PhoneNumberFormat::RFC3966);
        $attributes = [];
        $linkText = htmlspecialchars((string)$tel);
        /** @var RenderingContext $renderingContext */
        $renderingContext = $this->renderingContext;
        $request = $renderingContext->getRequest();
        if ($request instanceof ServerRequestInterface && ApplicationType::fromRequest($request)->isFrontend()) {
            // If there is no request, backend is assumed.
            /** @var TypoScriptFrontendController $frontend */
            $frontend = $GLOBALS['TSFE'];
            // passing HTML encoded link text
            try {
                $linkResult = GeneralUtility::makeInstance(LinkFactory::class)->create($linkText, ['parameter' => $linkHref], $frontend->cObj);
                $linkText = (string)$linkResult->getLinkText();
                $attributes = $linkResult->getAttributes();
            } catch (UnableToLinkException) {
                // Just render the email as is (= Backend Context), if LinkBuilder failed
            }
        }

        $tagContent = $this->renderChildren();
        if ($tagContent !== null) {
            $linkText = $tagContent;
        }

        $this->tag->setContent($linkText);
        $this->tag->addAttribute('href', $linkHref);
        $this->tag->forceClosingTag(true);
        $this->tag->addAttributes($attributes);
        return $this->tag->render();
    }
}
