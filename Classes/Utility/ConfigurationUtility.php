<?php

namespace TRAW\VhsCol\Utility;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility as TYPO3GeneralUtility;

class ConfigurationUtility
{
    /**
     * Get the typoscript configuration
     *
     * @return array
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public static function getConfiguration(string $extKey = 'vhs_col', bool $returnFullConfiguration = false, bool $throwException = true): array
    {
        $configurationManager = TYPO3GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Configuration\ConfigurationManager::class);
        $configuration = $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);

        $tsExtKey = GeneralUtility::getTyposcriptExtensionKey($extKey);

        $settings = $configuration['plugin.'][$tsExtKey . '.']['settings.'] ?? null;

        if (empty($settings) && $throwException) {
            throw new \Exception(TYPO3GeneralUtility::underscoredToUpperCamelCase($extKey) . ' Typoscript is missing! Add EXT:' . $extKey . ' Typoscript to your websites static template.');
        }

        return $returnFullConfiguration ? self::convert($configuration) : self::convert($settings);
    }

    /**
     * @param string $extKey
     *
     * @return array
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public static function getViewSettings(string $extKey = 'vhs_col'): array
    {
        return self::getConfiguration($extKey)['view'] ?? [];
    }

    /**
     * @param string $extKey
     *
     * @return array
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public static function getPersistenceSettings(string $extKey = 'vhs_col'): array
    {
        return self::getConfiguration($extKey)['persistence'] ?? [];
    }

    /**
     * @return array
     */
    public static function getSettings(string $extKey = 'vhs_col'): array
    {
        return self::getConfiguration($extKey);
    }

    /**
     * @param string $extKey
     * @param string $variable
     *
     * @return mixed
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public static function getEmConfigurationSettings(string $extKey, string $variable = ''): mixed
    {
        $extConf = TYPO3GeneralUtility::makeInstance(ExtensionConfiguration::class);

        return empty($variable)
            ? $extConf->get($extKey)
            : $extConf->get($extKey, $variable);
    }

    /**
     * Convert the typoscript array to a plain array
     *
     * @param array $configuration
     *
     * @return array
     */
    protected static function convert(array $configuration): array
    {
        return TYPO3GeneralUtility::makeInstance(TypoScriptService::class)
            ->convertTypoScriptArrayToPlainArray($configuration);
    }
}