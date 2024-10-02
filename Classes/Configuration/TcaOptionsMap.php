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

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 *
 */
class TcaOptionsMap
{

    /**
     * Usage:
     * Add a map to your table with conditions for when the items should be (or should not be) added
     * @var array
     */


    protected array $mapping = [];

    /**
     * @var array
     */
    protected array $properties = [];

    /**
     * @var array
     */
    protected array $items = [];

    /**
     * @var string
     */
    protected string $field = '';

    /**
     * @param array $params
     *
     * @return void
     */
    public function addOptions(array &$params): void
    {
        $this->initialize($params);
        $this->setOptions();
    }

    /**
     * @param array $params
     *
     * @return void
     */
    protected function initialize(array &$params): void
    {
        $table = $params['table'];

        $this->mapping = $GLOBALS['TCA'][$table]['tx_vhscol_option_map'] ?? [];
        $this->properties = $params['row'];

        foreach ($this->mapping as $propertyName => $mapping) {
            if (isset($this->properties[$propertyName])) {
                //if it's an array (tt_content) use the first entry
                $this->properties[$propertyName] = $this->properties[$propertyName][0]
                    ?? $this->properties[$propertyName]
                    ?? '';
            }
        }

        $this->items = &$params['items'];
        $this->field = $params['field'];
    }

    /**
     * @return void
     */
    protected function setOptions(): void
    {
        if (!empty($this->mapping[$this->field])) {
            foreach ($this->mapping[$this->field] as $configuration) {
                if (!empty($configuration['conditions'])) {
                    if ($this->isConditionMatching($configuration['conditions'] ?? [])) {
                        $this->items = array_merge($this->items, $configuration['options'] ?? []);
                    }
                } else {
                    if (!empty($configuration['options'])) {
                        $this->items = array_merge($this->items, $configuration['options'] ?? []);
                    }
                }
            }
        }
    }

    /**
     * @param array $conditions
     *
     * @return bool
     */
    protected function isConditionMatching(array $conditions): bool
    {
        if (isset($conditions['fields'])) {
            foreach ($conditions['fields'] as $startField => $compareFields) {
                if (!isset($this->properties[$startField]) || in_array($this->properties[$startField][0] ?? $this->properties[$startField], $compareFields) === false) {
                    return false;
                }
            }
        }
        if (isset($conditions['notFields'])) {
            foreach ($conditions['notFields'] as $startField => $compareFields) {
                if (isset($this->properties[$startField]) && in_array($this->properties[$startField][0] ?? $this->properties[$startField], $compareFields) === true) {
                    return false;
                }
            }
        }
        if (isset($conditions['functions'])) {
            foreach ($conditions['functions'] as $function => $values) {
                if (!method_exists($this, $function) || $this->{$function}($values) === false) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @param array $configuration
     *
     * @return bool
     * @throws DBALException
     * @throws Exception
     */
    protected function parentPageProperties(array $configuration): bool
    {
        return $this->parentAnythingProperties($configuration, 'pages', 'pid');
    }

    /**
     * @param array $configuration
     *
     * @return bool
     * @throws DBALException
     * @throws Exception
     */
    protected function parentContainerProperties(array $configuration): bool
    {
        return $this->parentAnythingProperties($configuration, 'tt_content', 'tx_container_parent');
    }

    /**
     * @param array  $configuration
     * @param string $table
     * @param string $parentDetectionField
     *
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    protected function parentAnythingProperties(array $configuration, string $table, string $parentDetectionField): bool
    {
        $parentUid = $this->properties[$parentDetectionField][0] ?? $this->properties[$parentDetectionField] ?? false;

        if ($parentUid === false) return false;

        $result = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable($table)
            ->select(
                array_merge(['uid'], array_keys($configuration)),
                $table,
                ['uid' => $parentUid]
            )->fetchAssociative();

        if (empty($result) || $result === false) return false;

        foreach ($configuration as $property => $values) {
            if (array_key_exists($property, $result) === false || in_array($result[$property], $values) === false) {
                return false;
            }
        }

        return true;
    }
}
