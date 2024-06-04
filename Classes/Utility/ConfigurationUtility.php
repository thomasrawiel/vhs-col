<?php

namespace TRAW\VhsCol\Utility;

use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConfigurationUtility
{
    /**
     * Get the typoscript configuration
     *
     * @return array
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public static function getConfiguration(): array
    {
        $configurationManager = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Configuration\ConfigurationManager::class);
        $configuration = $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);

        return $configuration['plugin.']['tx_linvhs.']['settings.'];
    }

    /**
     * Convert the typoscript array to a plain array
     * @return array
     */
    public static function getSettings(): array
    {
        return GeneralUtility::makeInstance(TypoScriptService::class)
            ->convertTypoScriptArrayToPlainArray(self::getConfiguration());
    }
}