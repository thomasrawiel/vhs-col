<?php

namespace TRAW\VhsCol\DataProcessing\Menu;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class SplitMenuProcessor
 */
class SplitMenuProcessor extends AbstractSplitMenuProcessor
{
    /**
     * @return array
     */

    public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData)
    {
        if (isset($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }

        $this->processorConfiguration = $processorConfiguration;
        $this->cObj = $cObj;

        $maxSplits = (int)$this->getConfigurationValue('maxSplits');
        $keepSpacersAfterSplit = ((int)$this->getConfigurationValue('keepSpacersAfterSplit'))
            && $this->getConfigurationValue('includeSpacers');

        $processorConfiguration['includeSpacer'] = $this->getConfigurationValue('includeSpacer') ? : 1;
        $processedData = parent::process($cObj, $contentObjectConfiguration, $processorConfiguration, $processedData);

        $splitMenu = $this->removeSpacers($this->splitMenu($processedData[$this->menuTargetVariableName] ?? [], $maxSplits), $keepSpacersAfterSplit);

        if ($splitMenu !== []) {
            $processedData[$this->menuTargetVariableName] = $splitMenu;
        }

        return $processedData;
    }

    protected function splitMenu(array $menu, int $maxSplits = 0): array
    {
        if ($maxSplits === 0) {
            return $menu;
        }

        $spacerKeys = array_keys(array_filter($menu, fn($item): bool => $item['spacer'] === 1));
        if (count($spacerKeys) > $maxSplits) {
            $spacerKeys = array_slice($spacerKeys, 0, $maxSplits);
        }

        $menuKeys = array_keys($menu);
        $splitIndexes = array_map(fn($key): int|false => array_search($key, $menuKeys, true), $spacerKeys);

        $splitMenu = [];
        $start = 0;
        foreach ($splitIndexes as $index) {
            $length = $index - $start;
            $part = array_slice($menu, $start, $length, true);
            $splitMenu[] = $part;
            $start = $index;
        }

        $splitMenu[] = array_slice($menu, $start, null, true);

        return $splitMenu;
    }

    protected function removeSpacers(array $menu, bool $keepSpacersAfterSplit = true): array
    {
        if ($keepSpacersAfterSplit) {
            return $menu;
        }

        foreach ($menu as $key => $part) {
            if (!is_array($part)) {
                continue;
            }

            $filtered = array_filter($part, fn($item): bool => ($item['spacer'] ?? 0) !== 1);
            $menu[$key] = array_values($filtered);
        }

        return $menu;
    }
}
