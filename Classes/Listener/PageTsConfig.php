<?php

declare(strict_types=1);

namespace TRAW\VhsCol\Listener;

use TRAW\VhsCol\Configuration\CTypes;
use TRAW\VhsCol\Helpers\PageTsGenerator;
use TRAW\VhsCol\Information\Typo3Version;
use TYPO3\CMS\Core\TypoScript\IncludeTree\Event\ModifyLoadedPageTsConfigEvent;

final class PageTsConfig
{
    /**
     * Generate Page tsconfig for registered CTypes
     *
     *
     * @throws \Exception
     */
    public function __invoke(ModifyLoadedPageTsConfigEvent $event): void
    {
        if (Typo3Version::getTypo3MajorVersion() < 12) {
            return;
        }

        $tsConfig = $event->getTsConfig();

        $generated = PageTsGenerator::generate();

        if ($generated !== '' && $generated !== '0') {
            $tsConfig = array_merge(['pagesTsConfig-package-vhscol' => $generated], $tsConfig);
            $event->setTsConfig($tsConfig);
        }

    }
}
