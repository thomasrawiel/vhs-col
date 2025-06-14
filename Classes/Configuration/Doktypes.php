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

            if (!empty($d->getIconIdentifier())) {
                $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes'][$d->getValue()] = $d->getIconIdentifier();
            }
            if (!empty($d->getIconIdentifierHide())) {
                $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes'][$d->getValue() . '-hideinmenu'] = $d->getIconIdentifierHide();
            }
            if (!empty($d->getIconIdentifierContentFromPid())) {
                $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes'][$d->getValue() . '-contentFromPid'] = $d->getIconIdentifierContentFromPid();
            }
            if (!empty($d->getIconIdentifierRoot())) {
                $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes'][$d->getValue() . '-root'] = $d->getIconIdentifierRoot();
            }

            $showitem = $d->getShowItem() ?? $GLOBALS['TCA']['pages']['types'][(string)$d->getItemType()]['showitem'] ?? '';

            if (!empty($d->getAdditionalShowitem())) {
                $showitem = $showitem . (str_starts_with($d->getAdditionalShowitem(), ',') ? '' : ',') . $d->getAdditionalShowitem();
            }

            $GLOBALS['TCA']['pages']['types'][(string)$d->getValue()]['showitem'] = $showitem;

            if (!empty($d->getColumnsOverrides())) {
                $GLOBALS['TCA']['pages']['types'][(string)$d->getValue()]['columnsOverrides'] = $d->getColumnsOverrides();
            }

            $GLOBALS['TCA']['pages']['tx_vhscol_doktypes'][$d->getValue()] = $doktype;
        }
    }

    /**
     * @throws \Exception
     */
    public static function registerDoktypesAfterBootCompleted(): void
    {
        $doktypes = $GLOBALS['TCA']['pages']['tx_vhscol_doktypes'] ?? null;
        if (!empty($doktypes)) {
            $dokTypeRegistry = GeneralUtility::makeInstance(PageDoktypeRegistry::class);
            $registerDoktypeInTSConfig = [];

            foreach ($doktypes as $doktype) {
                $d = null;
                if ($doktype instanceof Doktype || is_array($doktype) && !empty($doktype)) {
                    if (is_array($doktype)) {
                        $d = new Doktype($doktype);
                    } else {
                        $d = $doktype;
                    }
                }

                $dokTypeRegistry->add(
                    $d->getValue(),
                    [
                        'allowedTables' => $d->getAllowedTables() ?? '*',
                    ],
                );

                if ($d->isRegisterInDragArea()) {
                    $registerDoktypeInTSConfig[] = $d->getValue();
                }
            }

            $doktypesString = implode(',', $registerDoktypeInTSConfig);

            if (!empty($doktypesString)) {
                ExtensionManagementUtility::addUserTSConfig('options.pageTree.doktypesToShowInNewPageDragArea := addToList(' . $doktypesString . ')');
            }
        }
    }
}
