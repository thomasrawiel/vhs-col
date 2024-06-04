<?php

namespace TRAW\VhsCol\ViewHelpers\Uri;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class TelephoneViewHelper
 */
class TelephoneViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        $this->registerArgument('phoneNumber', 'string', 'The phone number to convert to a link', true, '');
    }

    /**
     * @return string
     */
    public function render()
    {
        $phoneUtil = PhoneNumberUtil::getInstance();

        //Phone Number string from database
        $inputNumberString = $this->arguments['phoneNumber'];

        try {
            $phoneNumber = $phoneUtil->parse($inputNumberString);
            //format as RFC3966 phone number with "tel:"-prefix
            return $phoneUtil->format($phoneNumber, PhoneNumberFormat::RFC3966);
        } catch (NumberParseException $e) {
            return '';
        }
    }
}
