<?php
declare(strict_types=1);

namespace TRAW\VhsCol\DataProcessing\Menu;

use TRAW\VhsCol\Information\Typo3Version;
use TYPO3\CMS\Frontend\DataProcessing\MenuProcessor;

if (TYPO3Version::getTypo3Version() < 14) {
    abstract class AbstractSplitMenuProcessor extends MenuProcessor
    {
        public $menuDefaults = [
            'levels' => 1,
            'expandAll' => 1,
            'includeSpacer' => 0,
            'keepSpacersAfterSplit' => 0,
            'maxSplits' => 1,
            'as' => 'menu',
            'titleField' => 'nav_title // title',
        ];

        /**
         * @var string[]
         */
        public $allowedConfigurationKeys = [
            'cache',
            'cache.',
            'cache_period',
            'entryLevel',
            'entryLevel.',
            'special',
            'special.',
            'minItems',
            'minItems.',
            'maxItems',
            'maxItems.',
            'begin',
            'begin.',
            'alternativeSortingField',
            'alternativeSortingField.',
            'showAccessRestrictedPages',
            'showAccessRestrictedPages.',
            'excludeUidList',
            'excludeUidList.',
            'excludeDoktypes',
            'includeNotInMenu',
            'includeNotInMenu.',
            'alwaysActivePIDlist',
            'alwaysActivePIDlist.',
            'protectLvar',
            'addQueryString',
            'addQueryString.',
            'if',
            'if.',
            'levels',
            'levels.',
            'expandAll',
            'expandAll.',
            'includeSpacer',
            'includeSpacer.',
            'as',
            'titleField',
            'titleField.',
            'dataProcessing',
            'dataProcessing.',
            'maxSplits',
            'keepSpacersAfterSplit',
        ];
    }
} else {
    abstract class AbstractSplitMenuProcessor extends MenuProcessor
    {
        public array $menuDefaults = [
            'levels' => 1,
            'expandAll' => 1,
            'includeSpacer' => 0,
            'keepSpacersAfterSplit' => 0,
            'maxSplits' => 1,
            'as' => 'menu',
            'titleField' => 'nav_title // title',
        ];

        /**
         * @var string[]
         */
        public array $allowedConfigurationKeys = [
            'cache',
            'cache.',
            'cache_period',
            'entryLevel',
            'entryLevel.',
            'special',
            'special.',
            'minItems',
            'minItems.',
            'maxItems',
            'maxItems.',
            'begin',
            'begin.',
            'alternativeSortingField',
            'alternativeSortingField.',
            'showAccessRestrictedPages',
            'showAccessRestrictedPages.',
            'excludeUidList',
            'excludeUidList.',
            'excludeDoktypes',
            'includeNotInMenu',
            'includeNotInMenu.',
            'alwaysActivePIDlist',
            'alwaysActivePIDlist.',
            'protectLvar',
            'addQueryString',
            'addQueryString.',
            'if',
            'if.',
            'levels',
            'levels.',
            'expandAll',
            'expandAll.',
            'includeSpacer',
            'includeSpacer.',
            'as',
            'titleField',
            'titleField.',
            'dataProcessing',
            'dataProcessing.',
            'maxSplits',
            'keepSpacersAfterSplit',
        ];
    }
}