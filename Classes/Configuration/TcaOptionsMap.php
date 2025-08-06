<?php

/**
 * Copyright notice
 *
 * (c) 2022 Thomas Rawiel <t.rawiel@lingner.com>
 *
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 *
 * Last modified: 03.03.22, 11:12
 */
/**
 * adapted from https://www.in2code.de/aktuelles/typo3-tt-contentlayout-und-frame-class-optionen-dynamisch-setzen/
 */
declare(strict_types=1);

namespace TRAW\VhsCol\Configuration;

class TcaOptionsMap
{
    /**
     * Usage:
     * Add a map to your table with conditions for when the items should be (or should not be) added
     */
    protected array $mapping = [];

    protected array $properties = [];

    protected array $items = [];

    protected string $field = '';

    public function __construct(private readonly \TYPO3\CMS\Core\Database\ConnectionPool $connectionPool) {}

    public function addOptions(array &$params): void
    {
        $this->initialize($params);
        $this->setOptions();
    }

    protected function initialize(array &$params): void
    {
        $table = $params['table'];

        $this->mapping = $GLOBALS['TCA'][$table]['tx_vhscol_option_map'] ?? [];
        $this->properties = $params['row'];

        foreach (array_keys($this->mapping) as $propertyName) {
            if (isset($this->properties[$propertyName])) {
                //if it's an array (tt_content) use the first entry
                $this->properties[$propertyName] = $this->properties[$propertyName][0]
                    ?? $this->properties[$propertyName]
                    ?? '';
            }
        }
        /**
         * $params Reference to TCA field configuration array (expects ['items'] to be passed by reference)
         */
        $this->items = &$params['items'];
        $this->field = $params['field'];
    }

    protected function setOptions(): void
    {
        if (!empty($this->mapping[$this->field])) {
            foreach ($this->mapping[$this->field] as $configuration) {
                if (!empty($configuration['conditions'])) {
                    if ($this->isConditionMatching($configuration['conditions'])) {
                        $this->items = $this->mergeItems($configuration['options'] ?? []);
                    }
                } elseif (!empty($configuration['options'])) {
                    $this->items = $this->mergeItems($configuration['options']);
                }
            }
        }
    }

    /**
     * Merge items but ignore duplicate values
     */
    protected function mergeItems(array $options): array
    {
        $existingValues = [];
        foreach ($this->items as $item) {
            if (is_array($item) && isset($item['value'])) {
                $existingValues[] = $item['value'];
            } elseif ($item instanceof \TYPO3\CMS\Core\Schema\Struct\SelectItem) {
                $existingValues[] = $item->getValue();
            }
        }

        $mergedItems = $this->items;
        foreach ($options as $item) {
            $value = null;
            if (is_array($item) && isset($item['value'])) {
                $value = $item['value'];
            } elseif ($item instanceof \TYPO3\CMS\Core\Schema\Struct\SelectItem) {
                $value = $item->getValue();
            }

            if ($value !== null && !in_array($value, $existingValues, true)) {
                $mergedItems[] = $item;
                $existingValues[] = $value; // Avoid duplicates in input itself
            }
        }

        return $mergedItems;
    }

    protected function isConditionMatching(array $conditions): bool
    {
        foreach ($conditions['fields'] ?? [] as $startField => $compareFields) {
            $needle = $this->extractNeedle($this->properties[$startField] ?? null);

            if (!in_array($needle, $compareFields, true)) {
                return false;
            }
        }

        foreach ($conditions['notFields'] ?? [] as $startField => $compareFields) {
            $needle = $this->extractNeedle($this->properties[$startField] ?? null);

            if (in_array($needle, $compareFields, true)) {
                return false;
            }
        }

        foreach ($conditions['functions'] ?? [] as $function => $values) {
            if (!method_exists($this, $function) || $this->$function($values) === false) {
                return false;
            }
        }

        return true;
    }

    private function extractNeedle(mixed $value): mixed
    {
        return is_array($value) ? ($value[0] ?? null) : $value;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function parentPageProperties(array $configuration): bool
    {
        return $this->parentAnythingProperties($configuration, 'pages', 'pid');
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function parentContainerProperties(array $configuration): bool
    {
        return $this->parentAnythingProperties($configuration, 'tt_content', 'tx_container_parent');
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function parentNewsRecordProperties(array $configuration): bool
    {
        return $this->parentAnythingProperties($configuration, 'tx_news_domain_model_news', 'tx_news_related_news');
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function parentAnythingProperties(array $configuration, string $table, string $parentDetectionField): bool
    {
        $parentUid = is_array($this->properties[$parentDetectionField]) ? $this->properties[$parentDetectionField][0] : ($this->properties[$parentDetectionField] ?? false);

        if ($parentUid === false) {
            return false;
        }

        $result = $this->connectionPool
            ->getConnectionForTable($table)
            ->select(
                array_merge(['uid'], array_keys($configuration)),
                $table,
                ['uid' => $parentUid]
            )->fetchAssociative();

        if (empty($result) || $result === false) {
            return false;
        }

        foreach ($configuration as $property => $values) {
            if (array_key_exists($property, $result) === false || in_array($result[$property], $values) === false) {
                return false;
            }
        }

        return true;
    }
}
