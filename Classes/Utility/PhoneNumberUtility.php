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
    public const PhoneNumberFormat defaultFormat = PhoneNumberFormat::RFC3966;

    public static function parsePhoneNumber(string $phoneNumberOrUriString, PhoneNumberFormat|int|null $format = null, ?string $region = null): string
    {
        $phoneUtil = PhoneNumberUtil::getInstance();

        $input = str_starts_with($phoneNumberOrUriString, 'tel:')
            ? substr($phoneNumberOrUriString, 4)
            : $phoneNumberOrUriString;

        try {
            $phoneNumber = $phoneUtil->parse($input, $region);
        } catch (NumberParseException) {
            return '';
        }

        if ($format instanceof PhoneNumberFormat) {
            $enumFormat = $format;
        } elseif (is_int($format)) {
            $enumFormat = PhoneNumberFormat::tryFrom($format) ?? self::defaultFormat;
        } else {
            $enumFormat = self::defaultFormat;
        }

        $formattedNumber = $phoneUtil->format($phoneNumber, $enumFormat);

        return $enumFormat === PhoneNumberFormat::RFC3966
            ? $formattedNumber
            : 'tel:' . $formattedNumber;
    }
}
