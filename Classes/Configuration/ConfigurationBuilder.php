<?php

declare(strict_types=1);

namespace TRAW\VhsCol\Configuration;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConfigurationBuilder implements SingletonInterface
{
    protected array $settings = [];

    protected array $persistenceSettings = [];

    protected array $viewSettings = [];

    public function __construct(protected ?\TYPO3\CMS\Extbase\Configuration\ConfigurationManager $configurationManager, protected ?\TYPO3\CMS\Core\TypoScript\TypoScriptService $typoScriptService) {}

    public function getSettings(string $extKey = 'vhs_col'): array
    {
        if ($this->settings === []) {
            $this->loadTypoScript($extKey);
        }

        return $this->settings;
    }

    public function getPersistenceSettings(string $extKey = 'vhs_col'): array
    {
        if ($this->persistenceSettings === []) {
            $this->loadTypoScript($extKey);
        }

        return $this->persistenceSettings;
    }

    public function getViewSettings(string $extKey = 'vhs_col'): array
    {
        if ($this->viewSettings === []) {
            $this->loadTypoScript($extKey);
        }

        return $this->viewSettings;
    }

    protected function loadTypoScript(string $extKey = 'vhs_col'): void
    {
        $tsExtKey = \TRAW\VhsCol\Utility\GeneralUtility::getTyposcriptExtensionKey($extKey);
        $typoscriptService = $this->getTypoScriptService();
        if ($GLOBALS['TYPO3_REQUEST']->getAttribute('frontend.typoscript')->getSetupArray()['plugin.'][$tsExtKey . '.']) {
            $typoScript = $typoscriptService->convertTypoScriptArrayToPlainArray($GLOBALS['TYPO3_REQUEST']->getAttribute('frontend.typoscript')->getSetupArray()['plugin.'][$tsExtKey . '.']);
        } else {
            $fullTypoScript = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
            $typoScript = $typoscriptService->convertTypoScriptArrayToPlainArray($fullTypoScript['plugin.'][$tsExtKey . '.']);
        }

        $this->settings = $typoScript['settings'];
        $this->persistenceSettings = $typoScript['persistence'] ?? [];
        $this->viewSettings = $typoScript['view'] ?? [];
    }

    /**
     * this method is taken from the old implementation in AbstractRepository. The reason this exists is that if
     * somehow the inject doesn't work, we still have a working TypoScriptService
     */
    protected function getTypoScriptService(): TypoScriptService
    {
        if (is_null($this->typoScriptService)) {
            $this->typoScriptService = GeneralUtility::makeInstance(\TYPO3\CMS\Core\TypoScript\TypoScriptService::class);
        }

        return $this->typoScriptService;
    }
}
