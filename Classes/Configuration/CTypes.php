<?php
declare(strict_types=1);

namespace TRAW\VhsCol\Configuration;

/**
 * Class CTypes
 */
class CTypes
{
    /**
     *
     *
     * @param array  $cTypes
     *
     * @throws \Exception
     */
    public static function registerCTypes(array $cTypes): void
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
                throw new \Exception('CType must be an instance of CType or array');
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
                \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItemGroup('tt_content', 'CType', $c->getGroup(), $c->getGroup());
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

            if ($c->getRegisterInNewContentElementWizard()) {
                $GLOBALS['TCA']['tt_content']['tx_vhscol_ctypes'][$c->getValue()] = $cType;
            }
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
        if (\TRAW\VhsCol\Information\Typo3Version::getTypo3MajorVersion() > 12) {
            // s. https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/13.0/Breaking-102834-RemoveItemsFromNewContentElementWizard.html
            return $pageTs;
        }
        if (!empty($GLOBALS['TCA']['tt_content']['tx_vhscol_ctypes'])) {
            foreach ($GLOBALS['TCA']['tt_content']['tx_vhscol_ctypes'] as $cTypeKey => $configuration) {
                $cType = new \TRAW\VhsCol\Configuration\TCA\CType($configuration);
                if ($cType->getRegisterInNewContentElementWizard()) {
                    $group = $cType->getGroup() ?? 'default';
                    $groupLabel = $GLOBALS['TCA']['tt_content']['columns']['CType']['config']['itemGroups'][$group] ?? $group;
                    $pageTs = '';
                    if (!in_array($group, ['common', 'default' . 'menu', 'special', 'forms', 'plugins'])) {
                        // do not override EXT:backend dummy placeholders for item groups
                        $pageTs .= 'mod.wizards.newContentElement.wizardItems.' . $group . '.header = ' . $groupLabel . LF;
                    }
                    $ttContentDefValues = 'CType = ' . $cType->getValue() . LF . implode(LF, $cType->getDefaultValues());
                    $pageTs .= 'mod.wizards.newContentElement.wizardItems.' . ($group === 'default' ? 'common' : $group) . '.elements {' . LF;
                    $pageTs .= '  ' . $cType->getValue() . ' {' . LF
                        . '    title = ' . $cType->getLabel() . LF
                        . '    description = ' . $cType->getDescription() . LF
                        . '    iconIdentifier = ' . $cType->getIconIdentifier() . LF
                        . '      tt_content_defValues {' . LF
                        . '          ' . $ttContentDefValues . LF
                        . '     }' . LF
                        . '      }' . LF
                        . '}' . LF;

                    $pageTs .= 'mod.wizards.newContentElement.wizardItems.' . ($group === 'default' ? 'common' : $group) . '.show := addToList(' . $cType->getValue() . ')' . LF;
                }
            }
        }
        return $pageTs;
    }
}
