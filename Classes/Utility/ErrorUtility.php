<?php

namespace TRAW\VhsCol\Utility;

/*
 * This file is adapted from the FluidTYPO3/Vhs project
 */

use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

/**
 * Error Utility
 *
 * Utility to assist with error throwing on different TYPO3 version
 */
class ErrorUtility
{
    public static function throwViewHelperException(?string $message = null, ?int $code = null): void
    {
        throw new Exception((string)$message, (int)$code);
    }
}
