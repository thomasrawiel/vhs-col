<?php
declare(strict_types=1);

namespace TRAW\VhsCol\Information;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Note: MUST NOT be used in the extension related early
 * bootstrap script files ext_localconf.php, ext_tables.php and Configuration/TCA/*
 *
 * @link    https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/11.0/Deprecation-92947-DeprecateTYPO3_MODEAndTYPO3_REQUESTTYPEConstants.html
 *
 * Class Request
 * @package TRAW\VhsCol\Information
 */
class RequestType
{
    /**
     * @return bool
     */
    public static function isFrontend(ServerRequestInterface $request = null): bool
    {
        return \TYPO3\CMS\Core\Http\ApplicationType::fromRequest(self::fetchRequest($request))->isFrontend();
    }

    /**
     * @return bool
     */
    public static function isBackend(ServerRequestInterface $request = null): bool
    {
        return \TYPO3\CMS\Core\Http\ApplicationType::fromRequest(self::fetchRequest($request))->isBackend();
    }

    /**
     * @param ServerRequestInterface|null $request
     * @param bool                        $abbreviate - return "BE" or ""FE" if true
     *
     * @return string
     */
    public static function getRequestType(ServerRequestInterface $request = null, bool $abbreviate = true): string
    {
        $applicationType = \TYPO3\CMS\Core\Http\ApplicationType::fromRequest(self::fetchRequest($request));

        return $abbreviate ? $applicationType->abbreviate() : $applicationType;
    }

    /**
     * @param ServerRequestInterface|null $request
     *
     * @return ServerRequestInterface
     */
    protected static function fetchRequest(ServerRequestInterface $request = null): ServerRequestInterface
    {
        $r = $request ?? $GLOBALS['TYPO3_REQUEST'] ?? null;

        if (empty($r) || !(($r ?? null) instanceof ServerRequestInterface)) {
            throw new Exception('No request object provided');
        }

        return $r;
    }

}
