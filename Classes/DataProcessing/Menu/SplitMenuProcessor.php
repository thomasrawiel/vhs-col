<?php

namespace TRAW\VhsCol\DataProcessing\Menu;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\DataProcessing\MenuProcessor;

class SplitMenuProcessor extends MenuProcessor
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

    public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData)
    {
        $this->processorConfiguration = $processorConfiguration;
        $this->cObj = $cObj;

        $maxSplits = (int)$this->getConfigurationValue('maxSplits');
        $keepSpacersAfterSplit = ((int)$this->getConfigurationValue('keepSpacersAfterSplit'))
            && $this->getConfigurationValue('includeSpacers');

        $processorConfiguration['includeSpacer'] = $this->getConfigurationValue('includeSpacer') ? : 1;
        $processedData = parent::process($cObj, $contentObjectConfiguration, $processorConfiguration, $processedData);

        $splitMenu = $this->removeSpacers($this->splitMenu($processedData[$this->menuTargetVariableName] ?? [], $maxSplits), $keepSpacersAfterSplit);

        if (!empty($splitMenu)) {
            $processedData[$this->menuTargetVariableName] = $splitMenu;
        }

        return $processedData;
    }

    protected function splitMenu(array $menu, int $maxSplits = 0): array
    {
        if ($maxSplits === 0) {
            return $menu;
        }
        $spacers = array_filter($menu, function ($item) {
            return $item['spacer'] === 1;
        }, ARRAY_FILTER_USE_BOTH);

        if (count($spacers) > $maxSplits) {
            $spacers = array_slice($spacers, 0, $maxSplits);
        }

        $splitMenu = [];

        foreach ($spacers as $originalKey => $spacer) {
            $key = array_search($originalKey, array_keys($menu), true);
            if ($key !== false) {
                $splitMenu[] = array_slice($menu, $key, null, true);
            }
        }

        if (empty($splitMenu)) {
            return [];
        }

        $firstPart = array_diff_key($menu, ...$splitMenu);
        array_unshift($splitMenu, $firstPart);

        return $splitMenu;
    }

    protected function removeSpacers(array $menu, bool $keepSpacersAfterSplit = true): array
    {
        if ($keepSpacersAfterSplit) {
            return $menu;
        }

        foreach ($menu as $key => $part) {
            $part = array_filter($part, function ($item) {
                return ($item['spacer'] ?? 0) !== 1;
            });
            $menu[$key] = array_values($part);
        }
        return $menu;
    }
}