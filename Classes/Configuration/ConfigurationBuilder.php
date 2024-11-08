<?php
declare(strict_types=1);

namespace TRAW\VhsCol\Configuration;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConfigurationBuilder implements SingletonInterface
{
    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager|null
     */
    protected ?\TYPO3\CMS\Extbase\Configuration\ConfigurationManager $configurationManager = null;

    /**
     * @var \TYPO3\CMS\Core\TypoScript\TypoScriptService|null
     */
    protected ?\TYPO3\CMS\Core\TypoScript\TypoScriptService $typoScriptService = null;

    /**
     * @var array
     */
    protected array $settings = [];

    /**
     * @var array
     */
    protected array $persistenceSettings = [];

    /**
     * @var array
     */
    protected array $viewSettings = [];

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
     * @param string $extKey
     *
     * @return array
     */
    public function getPersistenceSettings(string $extKey = 'vhs_col'): array
    {
        if (empty($this->persistenceSettings)) {
            $this->loadTypoScript($extKey);
        }

        return $this->persistenceSettings;
    }

    /**
     * @param string $extKey
     *
     * @return array
     */
    public function getViewSettings(string $extKey = 'vhs_col'): array
    {
        if (empty($this->viewSettings)) {
            $this->loadTypoScript($extKey);
        }

        return $this->viewSettings;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManager $configurationManager
     *
     * @return void
     */
    public function injectConfigurationManager(\TYPO3\CMS\Extbase\Configuration\ConfigurationManager $configurationManager): void
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * @param \TYPO3\CMS\Core\TypoScript\TypoScriptService $typoScriptService
     *
     * @return void
     */
    public function injectTypoScriptService(\TYPO3\CMS\Core\TypoScript\TypoScriptService $typoScriptService): void
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
        $typoscriptService = $this->getTypoScriptService();
        if ($GLOBALS['TSFE']->tmpl->setup['plugin.'][$tsExtKey . '.']) {
            $typoScript = $typoscriptService->convertTypoScriptArrayToPlainArray($GLOBALS['TSFE']->tmpl->setup['plugin.'][$tsExtKey . '.']);
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
     *
     * @return \TYPO3\CMS\Core\TypoScript\TypoScriptService
     */
    protected function getTypoScriptService(): TypoScriptService
    {
        if (is_null($this->typoScriptService)) {
            $this->typoScriptService = GeneralUtility::makeInstance(\TYPO3\CMS\Core\TypoScript\TypoScriptService::class);
        }

        return $this->typoScriptService;
    }
}
