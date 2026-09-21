<?php

declare(strict_types=1);

namespace TRAW\VhsCol\Configuration;

use TRAW\VhsCol\Information\Typo3Version;

final class RootlineFields
{
    public static function addRootlineFields(): void
    {
        //removed with v13 - do nothing
        throw new \InvalidArgumentException('addRootlineFields has been removed with typo3 13, just remove this function call');
    }

}
