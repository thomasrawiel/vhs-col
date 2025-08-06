<?php

namespace TRAW\VhsCol\Listener;

use TRAW\VhsCol\Configuration\Doktypes;
use TYPO3\CMS\Core\Core\Event\BootCompletedEvent;

final class BootCompleted
{
    /**
     * @param BootCompletedEvent $event
     *
     * @throws \Exception
     */
    public function __invoke(BootCompletedEvent $event): void
    {
        Doktypes::registerDoktypesAfterBootCompleted();
    }
}
