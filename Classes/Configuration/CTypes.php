<?php
declare(strict_types=1);

namespace TRAW\VhsCol\Configuration;

use TRAW\VhsCol\Configuration\TCA\CType;
use TRAW\VhsCol\Information\Typo3Version;

/**
 * Class CTypes
 */
final class CTypes
{
    /**
     * @param array       $cTypes
     * @param string|null $selectItemGroupLabel
     *
     * @return void
     * @throws \Exception
     */
    public static function registerCTypes(array $cTypes, ?string $selectItemGroupLabel = null): void
    {
        //todo: sort original items by relativeposition and relativetofield
        foreach ($cTypes as $cType) {
            $c = null;
            if (($cType instanceof \TRAW\VhsCol\Configuration\TCA\CType || is_array($cType)) && !empty($cType)) {
                if (is_array($cType)) {
                    $c = new \TRAW\VhsCol\Configuration\TCA\CType($cType);
                } else {
                    $c = $cType;
                }
            } else {
                throw new \Exception('CType must be an instance of ' . CType::class . ' or array', 9552057115);
            }

            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
                'tt_content',
                'CType',
                [
                    'label' => $c->getLabel(),
                    'value' => $c->getValue(),
                    'icon' => $c->getIconIdentifier(),
                    'group' => $c->getGroup(),
                ],
                $c->getRelativeToField(),
                $c->getRelativePosition()
            );
            if (!empty($c->getGroup()) && empty($GLOBALS['TCA']['tt_content']['columns']['CType']['config']['itemGroups'][$c->getGroup()])) {
                \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItemGroup('tt_content', 'CType', $c->getGroup(), $selectItemGroupLabel ?? $c->getGroup());
            }
            if (!empty($c->getIconIdentifier())) {
                $GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes'][$c->getValue()] = $c->getIconIdentifier();
            }
            if (!empty($c->getShowitem())) {
                $GLOBALS['TCA']['tt_content']['types'][$c->getValue()]['showitem'] = $c->getShowitem();
            }
            if (!empty($c->getColumnsOverrides())) {
                $GLOBALS['TCA']['tt_content']['types'][$c->getValue()]['columnsOverrides'] = $c->getColumnsOverrides();
            }
            if (!empty($c->getFlexform())) {
                \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('', $c->getFlexform(), $c->getValue());
            }
            if (!empty($c->getPreviewRenderer()) && class_exists($c->getPreviewRenderer())) {
                $GLOBALS['TCA']['tt_content']['types'][$c->getValue()]['previewRenderer'] = $c->getPreviewRenderer();
            }
            if ($c->getSaveAndClose() && Typo3Version::getTypo3MajorVersion() > 12) {
                $GLOBALS['TCA']['tt_content']['types'][$c->getValue()]['creationOptions']['saveAndClose'] = true;
            }
            if (!empty($c->getDefaultValues()) && Typo3Version::getTypo3MajorVersion() > 12) {
                $GLOBALS['TCA']['tt_content']['types'][$c->getValue()]['creationOptions']['defaultValues'] = $c->getDefaultValues();
            }

            $GLOBALS['TCA']['tt_content']['tx_vhscol_ctypes'][$c->getValue()] = $cType;
        }
    }

    /**
     * Adapted from b13/container
     *
     * @return string
     * @throws \Exception
     */
    public static function getPageTsString(): string
    {
        $pageTs = '';
        if (!empty($GLOBALS['TCA']['tt_content']['tx_vhscol_ctypes'])) {
            $headerAddedGroups = [];

            foreach ($GLOBALS['TCA']['tt_content']['tx_vhscol_ctypes'] as $cTypeKey => $configuration) {
                $cType = new \TRAW\VhsCol\Configuration\TCA\CType($configuration);
                $headerAdded = array_key_exists($cType->getGroup(), $headerAddedGroups);
                $group = $cType->getGroup();
                if ($cType->getRegisterInNewContentElementWizard() && Typo3Version::getTypo3MajorVersion() < 13) {
                    $pageTs .= '### New content element wizard configuration for CType "' . $cType->getValue() . '"' . LF;
                    $groupLabel = $GLOBALS['TCA']['tt_content']['columns']['CType']['config']['itemGroups'][$group] ?? $group;

                    if (!in_array($group, ['common', 'default' . 'menu', 'special', 'forms', 'plugins']) && !$headerAdded) {
                        if(self::groupHasElements($group)) {
                            // do not override EXT:backend dummy placeholders for item groups
                            $pageTs .= 'mod.wizards.newContentElement.wizardItems.' . $group . '.header = ' . $groupLabel . LF;
                            $headerAddedGroups[$cType->getGroup()] = true;
                        }
                    }

                    $pageTs .= 'mod.wizards.newContentElement.wizardItems.' . ($group === 'default' ? 'common' : $group) . '.elements {' . LF;
                    $pageTs .= '  ' . $cType->getValue() . ' {' . LF
                        . '    title = ' . $cType->getLabel() . LF
                        . '    description = ' . $cType->getDescription() . LF
                        . '    iconIdentifier = ' . $cType->getIconIdentifier() . LF
                        . '      tt_content_defValues {' . LF
                        . '          ' . self::getDefaultValuesPageTsString($cType) . LF
                        . '     }' . LF
                        . '  }' . LF
                        . '}' . LF;

                    $pageTs .= 'mod.wizards.newContentElement.wizardItems.' . ($group === 'default' ? 'common' : $group) . '.show := addToList(' . $cType->getValue() . ')' . LF . LF;
                }

                if ($cType->getRegisterInNewContentElementWizard() === false && Typo3Version::getTypo3MajorVersion() > 12) {
                    $pageTs .= 'mod.wizards.newContentElement.wizardItems.' . $group . '.removeItems := addToList(' . $cType->getValue() . ')' . LF;
                }
            }
        }
        return $pageTs;
    }

    /**
     * 'defaultValues' => [
     *      'header_layout' => 2,
     *      'header_style' => 3,
     *      'sectionIndex' => 0,
     *      'nonexistentcolumnname' => 123,
     * ],
     *
     * column names that don't exist in TCA tt_content columns are skipped
     *
     *
     * @param CType $cType
     *
     * @return string
     */
    protected static function getDefaultValuesPageTsString(CType $cType): string
    {
        $pageTsString = 'CType = ' . $cType->getValue();;

        if (!empty($cType->getDefaultValues()) && Typo3Version::getTypo3MajorVersion() < 13) {
            $defaultValues = array_map(function ($value, $key) {
                if (!empty($GLOBALS['TCA']['tt_content']['columns'][$key])) {
                    return $key . ' = ' . $value;
                }
                return '';
            }, $cType->getDefaultValues(), array_keys($cType->getDefaultValues()));

            $pageTsString = $pageTsString . LF . '          ' . implode(LF . '          ', array_filter($defaultValues));
        }
        return $pageTsString;
    }

    /**
     *
     * @param string $group
     *
     * @return bool
     */
    protected static function groupHasElements(string $group): bool
    {
        $items = $GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'];

        foreach ($items as $item) {
            if (($item['group'] ?? null) === $group) {
                return true;
            }
        }

        return false;
    }
}
