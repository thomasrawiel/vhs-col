<?php

namespace TRAW\VhsCol\Listener;

use TRAW\VhsCol\Configuration\Doktypes;
use TYPO3\CMS\Core\Configuration\Event\AfterTcaCompilationEvent;

final class UserTsConfig
{
    public function __invoke(AfterTcaCompilationEvent  $event): void
    {
       $tca = $event->getTca();
       if(isset($tca['pages']['tx_vhscol_doktypes']) && !empty($tca['pages']['tx_vhscol_doktypes'])){
           Doktypes::registerDoktypesInUserTsConfig($tca['pages']['tx_vhscol_doktypes']);
       }
    }
}
