Lorem ipsum here and so on

..  code-block:: php
    :caption: EXT:site_package/Configuration/TCA/Overrides/sys_template.php

    /**
     * Add default TypoScript (constants and setup)
     */
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
         'site_package',
         'Configuration/TypoScript',
         'Site Package'
    );