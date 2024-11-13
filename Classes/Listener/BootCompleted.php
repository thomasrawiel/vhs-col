<?php

namespace TRAW\VhsCol\Listener;

use TRAW\VhsCol\Configuration\Doktypes;
use TYPO3\CMS\Core\Core\Event\BootCompletedEvent;

final class BootCompleted
{
    public function __invoke(BootCompletedEvent $event)
    {
        $doktypes = $GLOBALS['TCA']['pages']['tx_vhscol_doktypes'] ?? null;
        if (!empty($doktypes)) {
            Doktypes::registerDoktypesInExtTables($doktypes);
        }
    }
}
