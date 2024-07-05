<?php

namespace TRAW\VhsCol\Configuration;

use TRAW\VhsCol\Configuration\TCA\CType;

/**
 * Class CTypes
 * @package TRAW\VhsCol\Configuration
 */
class CTypes
{
    /**
     *
     *
     * @param array  $cTypes
     * @param string $table
     *
     * @return void
     * @throws \Exception
     */
    public static function registerCTypes(array $cTypes, string $table = 'tt_content'): void
    {
        //todo: sort original items by relativeposition and relativetofield
        foreach ($cTypes as $cType) {
            $c = null;
            if (($cType instanceof CType || is_array($cType)) && !empty($cType)) {
                if (is_array($cType)) {
                    $c = new CType($cType);
                } else {
                    $c = $cType;
                }
            } else {
                throw new \Exception('CType must be an instance of CType or array');
            }

            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
                $table,
                'CType',
                [
                    'label' => $c->getLabel(),
                    'value' => $c->getValue(),
                    'icon' => $c->getIcon(),
                    'group' => $c->getGroup(),
                ],
                $c->getRelativeToField(),
                $c->getRelativePosition()
            );
            if (!empty($c->getGroup()) && empty($GLOBALS['TCA'][$table]['columns']['CType']['config']['itemGroups'][$c->getGroup()])) {
                \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItemGroup($table, 'CType', $c->getGroup(), $c->getGroup());
            }
            if (!empty($c->getIcon())) {
                $GLOBALS['TCA'][$table]['ctrl']['typeicon_classes'][$c->getValue()] = $c->getIcon();
            }
            if (!empty($c->getShowitem())) {
                $GLOBALS['TCA'][$table]['types'][$c->getValue()]['showitem'] = $c->getShowitem();
            }
            if (!empty($c->getColumnsOverrides())) {
                $GLOBALS['TCA'][$table]['types'][$c->getValue()]['columnsOverrides'] = $c->getColumnsOverrides();
            }
            if (!empty($c->getFlexform())) {
                \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('', $c->getFlexform(), $c->getValue());
            }
        }
    }

    protected static function sortByRelativePosition(array &$cTypes)
    {

    }
}