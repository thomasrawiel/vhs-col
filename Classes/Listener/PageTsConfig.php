<?php
declare(strict_types=1);

namespace TRAW\VhsCol\Listener;

use TYPO3\CMS\Core\TypoScript\IncludeTree\Event\ModifyLoadedPageTsConfigEvent;

class PageTsConfig
{
    public function __invoke(ModifyLoadedPageTsConfigEvent $event): void
    {
        if (\TRAW\VhsCol\Information\Typo3Version::getTypo3MajorVersion() < 12) {
            return;
        }
        $tsConfig = $event->getTsConfig();
        $tsConfig = array_merge(['pagesTsConfig-package-vhscol' => \TRAW\VhsCol\Configuration\CTypes::getPageTsString()], $tsConfig);
        $event->setTsConfig($tsConfig);
    }
}
