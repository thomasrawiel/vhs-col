<?php
declare(strict_types=1);

namespace TRAW\VhsCol\Utility;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

/**
 * Class PhoneNumberUtility
 */
class PhoneNumberUtility
{
    /**
     *
     */
    public const defaultFormat = PhoneNumberFormat::RFC3966;

    /**
     * @param string      $phoneNumberOrUriString
     * @param int|null    $format
     * @param string|null $region
     *
     * @return string
     */
    public static function parsePhoneNumber(string $phoneNumberOrUriString, ?int $format = null, ?string $region = null): string
    {
        $phoneUtil = PhoneNumberUtil::getInstance();

        $inputNumberString = $phoneNumberOrUriString;

        //if it's already a uri, then we just want the number
        if (str_starts_with('tel:', $inputNumberString)) {
            $inputNumberString = substr($inputNumberString, 4);
        }

        try {
            $phoneNumber = $phoneUtil->parse($inputNumberString, $region);
        } catch (NumberParseException $e) {
            return '';
        }
        $allowedFormats = [
            PhoneNumberFormat::E164,
            PhoneNumberFormat::INTERNATIONAL,
            PhoneNumberFormat::NATIONAL,
            PhoneNumberFormat::RFC3966,
        ];
        if (!in_array($format, $allowedFormats, true)) {
            $format = self::defaultFormat;
        }
        $formattedNumber = $phoneUtil->format($phoneNumber, $format);

        if ($format === PhoneNumberFormat::RFC3966) {
            return $formattedNumber;
        }

        return 'tel:' . $formattedNumber;
    }
}