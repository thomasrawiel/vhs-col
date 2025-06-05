<?php
declare(strict_types=1);
namespace TRAW\VhsCol\ViewHelpers\Uri;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
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

    /**
     * @return string
     */
    public function render(): string
    {
        try {
            $inputNumberString = $this->arguments['phoneNumber'] ?? $this->renderChildren();
            $phoneUtil = PhoneNumberUtil::getInstance();

            //if it's already a uri, then we just want the number
            if (str_starts_with('tel:', $inputNumberString)) {
                $inputNumberString = substr($inputNumberString, 4);
            }

            $phoneNumber = $phoneUtil->parse($inputNumberString, $this->arguments['defaultRegion'] ?? null);

            //if we don't know the format, set rfc as format
            if (!in_array($this->arguments['format'], [0, 1, 2, 3])) {
                $this->arguments['format'] = 3;
            }

            return $phoneUtil->format($phoneNumber, $this->arguments['format']);
        } catch (NumberParseException $e) {
            return $e->getMessage();
        }
    }
}
