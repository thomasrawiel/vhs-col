<?php

declare(strict_types=1);

namespace TRAW\VhsCol\ViewHelpers\Uri;

use libphonenumber\PhoneNumberFormat;
use TRAW\VhsCol\Utility\PhoneNumberUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class TelephoneViewHelper
 *
 * Convert a phone number into a telephone uri
 *
 * Usage:
 * <vcol:uri.telephone phoneNumber="{data.header}" />
 * <vcol:uri.telephone phoneNumber="{data.header}" defaultRegion="de"/>
 * <vcol:uri.telephone phoneNumber="{data.header}" defaultRegion="de" format="3"/>
 *
 * Inline:
 * {data.header->vcol:uri.telephone()}
 * {data.header->vcol:uri.telephone(defaultRegion:'de')}
 * {data.header->vcol:uri.telephone(defaultRegion:'de',format:3)}
 */
class TelephoneViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('phoneNumber', 'string', 'The phone number to convert to a link');
        $this->registerArgument('defaultRegion', 'string', 'Region that we are expecting the number to be from');
        $this->registerArgument('format', 'integer', 'Phone number format', false, PhoneNumberFormat::RFC3966);
    }

    
    public function render(): string
    {
        $inputNumberString = $this->arguments['phoneNumber'] ?? $this->renderChildren();
        return PhoneNumberUtility::parsePhoneNumber(
            $inputNumberString,
            $this->arguments['format'] ?? null,
            $this->arguments['defaultRegion'] ?? null
        );
    }
}
