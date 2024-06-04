<?php

namespace TRAW\VhsCol\Configuration;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConfigurationBuilder implements SingletonInterface
{
    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager
     */
    protected \TYPO3\CMS\Extbase\Configuration\ConfigurationManager $configurationManager;

    /**
     * @var \TYPO3\CMS\Core\TypoScript\TypoScriptService
     */
    protected \TYPO3\CMS\Core\TypoScript\TypoScriptService $typoScriptService;

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var array
     */
    protected $persistenceSettings = [];

    /**
     * @var array
     */
    protected $viewSettings = [];

    /**
     * @param string $extKey
     *
     * @return array
     */
    public function getSettings(string $extKey = 'vhs_col'): array
    {
        if (empty($this->settings)) {
            $this->loadTypoScript($extKey);
        }

        return $this->settings;
    }

    /**
     * @return array
     */
    public function getPersistenceSettings()
    {
        if (!count($this->persistenceSettings)) {
            $this->loadTypoScript();
        }

        return $this->persistenceSettings;
    }

    /**
     * @return array
     */
    public function getViewSettings()
    {
        if (!count($this->viewSettings)) {
            $this->loadTypoScript();
        }

        return $this->viewSettings;
    }

    public function injectConfigurationManager(\TYPO3\CMS\Extbase\Configuration\ConfigurationManager $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    public function injectTypoScriptService(\TYPO3\CMS\Core\TypoScript\TypoScriptService $typoScriptService)
    {
        $this->typoScriptService = $typoScriptService;
    }

    /**
     * @param string $extKey
     *
     * @return void
     */
    protected function loadTypoScript(string $extKey = 'vhs_col'): void
    {
        $tsExtKey = \TRAW\VhsCol\Utility\GeneralUtility::getTyposcriptExtensionKey($extKey);

        if ($GLOBALS['TSFE']->tmpl->setup['plugin.'][$tsExtKey . '.']) {
            $typoScript = $this->getTypoScriptService()
                ->convertTypoScriptArrayToPlainArray($GLOBALS['TSFE']->tmpl->setup['plugin.'][$tsExtKey . '.']);
        } else {
            $fullTypoScript = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
            $typoScript = $this->getTypoScriptService()
                ->convertTypoScriptArrayToPlainArray($fullTypoScript['plugin.'][$tsExtKey . '.']);
        }
        $this->settings = $typoScript['settings'];
        $this->persistenceSettings = $typoScript['persistence'] ?? [];
        $this->viewSettings = $typoScript['view'] ?? [];
    }

    /**
     * this method is taken from the old implementation in AbstractRepository. The reason this exists is that if
     * somehow the inject doesn't work, we still have a working TypoScriptService
     *
     * @return \TYPO3\CMS\Core\TypoScript\TypoScriptService
     */
    protected function getTypoScriptService()
    {
        if (is_null($this->typoScriptService)) {
            $this->typoScriptService = GeneralUtility::makeInstance(\TYPO3\CMS\Core\TypoScript\TypoScriptService::class);
        }

        return $this->typoScriptService;
    }
}
