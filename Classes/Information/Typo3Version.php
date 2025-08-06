<?php

declare(strict_types=1);

namespace TRAW\VhsCol\Information;

class Typo3Version
{
    public static function getTypo3Version(): string
    {
        return (new \TYPO3\CMS\Core\Information\Typo3Version())->getVersion();
    }

    public static function getTypo3MajorVersion(): int
    {
        return (new \TYPO3\CMS\Core\Information\Typo3Version())->getMajorVersion();
    }

    /**
     * @param string|null $operator #[ExpectedValues(values: ["<", "lt", "<=", "le", ">", "gt", ">=", "ge", "==", "=", "eq", "!=", "<>", "ne",])]
     * @return bool|int -1 if the current TYPO3 version is lower than the provided,
     *                  0 if they are equal, and
     *                  1 if the provided version is lower than the current TYPO3 verion.
     *
     *                  When using the optional operator argument, the
     *                  function will return true if the relationship is the one specified
     *                  by the operator, false otherwise.
     */
    public static function compareCurrentTypo3Version(string $version, ?string $operator): bool|int
    {
        return version_compare(Typo3Version::getTypo3Version(), $version, $operator);
    }
}
