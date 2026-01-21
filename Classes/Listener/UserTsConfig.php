<?php
declare(strict_types=1);

namespace TRAW\VhsCol\Listener;

use TRAW\VhsCol\Configuration\Doktypes;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\TypoScript\IncludeTree\Event\BeforeLoadedUserTsConfigEvent;

#[AsEventListener(
    identifier: 'txvhscol-user-tsconfig'
)]
final class UserTsConfig
{
    public function __invoke(BeforeLoadedUserTsConfigEvent $event)
    {
        $doktypeUserTsConfigString = Doktypes::registerDoktypesInDragArea();

        if (!empty($doktypeUserTsConfigString)) {
            $event->addTsConfig($doktypeUserTsConfigString);
        }
    }
}
