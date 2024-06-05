<?php

namespace TRAW\VhsCol\Utility;

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
    public static function getConfiguration(string $extKey = 'vhs_col'): array
    {
        $configurationManager = TYPO3GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Configuration\ConfigurationManager::class);
        $configuration = $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);

        $tsExtKey = 'tx_' . GeneralUtility::underscoredToLowerCase($extKey);

        $settings = $configuration['plugin.'][$tsExtKey . '.']['settings.'] ?? null;

        if (empty($settings)) {
            throw new \Exception(TYPO3GeneralUtility::underscoredToUpperCamelCase($extKey) . ' Typoscript is missing! Add EXT:' . $extKey . ' Typoscript to your websites static template.');
        }

        return $settings;
    }

    /**
     * Convert the typoscript array to a plain array
     * @return array
     */
    public static function getSettings(string $extKey = 'vhs_col'): array
    {


        return TYPO3GeneralUtility::makeInstance(TypoScriptService::class)
            ->convertTypoScriptArrayToPlainArray(self::getConfiguration());
    }
}