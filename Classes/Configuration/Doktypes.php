<?php

namespace TRAW\VhsCol\Configuration;

use TRAW\VhsCol\Configuration\TCA\Doktype;
use TYPO3\CMS\Core\DataHandling\PageDoktypeRegistry;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Doktypes
 */
final class Doktypes
{
    /**
     * Call in TCA/Overrides/pages.php
     *
     * @param array       $doktypes
     * @param string|null $groupLabel
     *
     * @return void
     * @throws \Exception
     */
    public static function registerDoktypes(array $doktypes, ?string $groupLabel = null): void
    {
        foreach ($doktypes as $doktype) {
            $d = null;
            if ($doktype instanceof Doktype || is_array($doktype) && !empty($doktype)) {
                if (is_array($doktype)) {
                    $d = new Doktype($doktype);
                } else {
                    $d = $doktype;
                }
            }

            if (!isset($GLOBALS['TCA']['pages']['columns']['doktype']['config']['itemGroups'][$d->getGroup()])) {
                ExtensionManagementUtility::addTcaSelectItemGroup('pages', 'doktype', $d->getGroup(), $groupLabel ?? $d->getGroup());
            }

            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
                'pages',
                'doktype',
                [
                    'label' => $d->getLabel(),
                    'value' => $d->getValue(),
                    'icon' => $d->getIconIdentifier(),
                    'group' => $d->getGroup(),
                ],
            );
            $typeIconClasses = &$GLOBALS['TCA']['pages']['ctrl']['typeicon_classes'];
            if (!empty($d->getIconIdentifier())) {
                $typeIconClasses[$d->getValue()] = $d->getIconIdentifier();
            }
            if (!empty($d->getIconIdentifierHide())) {
                $typeIconClasses[$d->getValue() . '-hideinmenu'] = $d->getIconIdentifierHide();
            }
            if (!empty($d->getIconIdentifierContentFromPid())) {
                $typeIconClasses[$d->getValue() . '-contentFromPid'] = $d->getIconIdentifierContentFromPid();
            }
            if (!empty($d->getIconIdentifierRoot())) {
                $typeIconClasses[$d->getValue() . '-root'] = $d->getIconIdentifierRoot();
            }

            $types = &$GLOBALS['TCA']['pages']['types'];

            $showitem = $types[(string)$d->getItemType()]['showitem'] ?? '';

            if (!empty($d->getAdditionalShowitem())) {
                $showitem = $showitem . (str_starts_with($d->getAdditionalShowitem(), ',') ? '.' : '') . $d->getAdditionalShowitem();
            }

            $types[(string)$d->getValue()]['showitem'] = $showitem;

            if (!empty($d->getColumnsOverrides())) {
                $types[(string)$d->getValue()]['columnsOverrides'] = $d->getColumnsOverrides();
            }

            $GLOBALS['TCA']['pages']['tx_vhscol_doktypes'][$d->getValue()] = $doktype;
        }
    }

    /**
     * Call this in ext_tables.php
     *
     * @param array $doktypes
     *
     * @return void
     */
    public static function registerDoktypesInExtTables(array $doktypes): void
    {
        $dokTypeRegistry = GeneralUtility::makeInstance(PageDoktypeRegistry::class);
        foreach ($doktypes as $doktype) {
            $dokTypeRegistry->add(
                $doktype['value'],
                [
                    'allowedTables' => $doktype['allowedTables'] ?? '*',
                ],
            );
        }
    }

    /**
     * Call this in ext_localconf.php
     * @return string
     */
    public static function registerDoktypesInUserTsConfig(array $doktypes): void
    {
        $register = [];
        
        foreach($doktypes as $doktype) {
            $d = null;
            if ($doktype instanceof Doktype || is_array($doktype) && !empty($doktype)) {
                if (is_array($doktype)) {
                    $d = new Doktype($doktype);
                } else {
                    $d = $doktype;
                }
            }

            if($d->isRegisterInDragArea()) {
                $register[] = $d->getValue();
            }
        }
        
        $doktypesString = implode(',', $register);

        if (!empty($doktypesString)) {
            ExtensionManagementUtility::addUserTSConfig('options.pageTree.doktypesToShowInNewPageDragArea := addToList(' . $doktypesString . ')');
        }
    }
}
