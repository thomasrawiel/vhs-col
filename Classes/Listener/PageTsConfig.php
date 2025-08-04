<?php
declare(strict_types=1);

namespace TRAW\VhsCol\Listener;

use TRAW\VhsCol\Helpers\PageTsGenerator;
use TYPO3\CMS\Core\TypoScript\IncludeTree\Event\ModifyLoadedPageTsConfigEvent;
use TRAW\VhsCol\Information\Typo3Version;
use TRAW\VhsCol\Configuration\CTypes;

final class PageTsConfig
{
    /**
     * Generate Page tsconfig for registered CTypes
     *
     * @param ModifyLoadedPageTsConfigEvent $event
     *
     * @return void
     * @throws \Exception
     */
    public function __invoke(ModifyLoadedPageTsConfigEvent $event): void
    {
        if (Typo3Version::getTypo3MajorVersion() < 12) {
            return;
        }
        $tsConfig = $event->getTsConfig();

        $generated = PageTsGenerator::generate();

        if(!empty($generated)) {
            $tsConfig = array_merge(['pagesTsConfig-package-vhscol' => PageTsGenerator::generate()], $tsConfig);
            $event->setTsConfig($tsConfig);
        }

    }
}
