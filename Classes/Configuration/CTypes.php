<?php
declare(strict_types=1);

namespace TRAW\VhsCol\Configuration;

use TRAW\VhsCol\Configuration\TCA\CType;
use TRAW\VhsCol\Information\Typo3Version;

final class CTypes
{
    public static function registerCTypes(array $cTypes, ?string $selectItemGroupLabel = null): void
    {
        foreach ($cTypes as $cType) {
            if (($cType instanceof \TRAW\VhsCol\Configuration\TCA\CType || is_array($cType)) && !empty($cType)) {
                if (is_array($cType)) {
                    $cType = new \TRAW\VhsCol\Configuration\TCA\CType($cType);
                } else {
                    $cType = $cType;
                }
            } else {
                throw new \Exception('CType must be an instance of ' . CType::class . ' or array', 9552057115);
            }


            self::validateCType($cType);
            self::registerSelectItem($cType, $selectItemGroupLabel);
            self::registerTcaTypeConfiguration($cType);
            self::registerIconIfAvailable($cType);
            self::registerCreationOptionsIfSupported($cType);

            self::storeCTypeForLaterUse($cType);
        }
    }

    private static function validateCType(CType $cType): void
    {
        if (trim($cType->getValue()) === '') {
            throw new \InvalidArgumentException('CType value must not be empty');
        }
        if (trim($cType->getLabel()) === '') {
            throw new \InvalidArgumentException('CType label must not be empty');
        }
    }

    private static function registerSelectItem(CType $cType, ?string $groupLabel): void
    {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
            'tt_content',
            'CType',
            [
                'label' => $cType->getLabel(),
                'description' => $cType->getDescription(),
                'value' => $cType->getValue(),
                'icon' => $cType->getIconIdentifier(),
                'group' => $groupLabel ?? $cType->getGroup(),
            ],
            $cType->getRelativeToField(),
            $cType->getRelativePosition()
        );
    }

    private static function registerTcaTypeConfiguration(CType $cType): void
    {
        $value = $cType->getValue();
        $typeConfig = [];

        if ($showItem = $cType->getShowItem()) {
            $typeConfig['showitem'] = $showItem;
        }

        if ($columnsOverrides = $cType->getColumnsOverrides()) {
            $typeConfig['columnsOverrides'] = $columnsOverrides;
        }

        if ($flexform = $cType->getFlexform()) {
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('', $cType->getFlexform(), $cType->getValue());
        }

        if ($previewRenderer = $cType->getPreviewRenderer()) {
            $typeConfig['previewRenderer'] = $previewRenderer;
        }

        if (!empty($typeConfig)) {
            $GLOBALS['TCA']['tt_content']['types'][$value] = array_replace_recursive(
                $GLOBALS['TCA']['tt_content']['types'][$value] ?? [],
                $typeConfig
            );
        }
    }

    private static function registerIconIfAvailable(CType $cType): void
    {
        $icon = $cType->getIconIdentifier();
        if ($icon) {
            $GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes'][$cType->getValue()] = $icon;
        }
    }

    private static function registerCreationOptionsIfSupported(CType $cType): void
    {
        if ($cType->getSaveAndClose() && Typo3Version::getTypo3MajorVersion() > 12) {
            $GLOBALS['TCA']['tt_content']['types'][$cType->getValue()]['creationOptions']['saveAndClose'] = true;
        }
        if (!empty($cType->getDefaultValues()) && Typo3Version::getTypo3MajorVersion() > 12) {
            $GLOBALS['TCA']['tt_content']['types'][$cType->getValue()]['creationOptions']['defaultValues'] = $cType->getDefaultValues();
        }
    }

    private static function storeCTypeForLaterUse(CType $cType): void
    {
        if (!isset($GLOBALS['TCA']['tt_content']['tx_vhscol_ctypes'])) {
            $GLOBALS['TCA']['tt_content']['tx_vhscol_ctypes'] = [];
        }

        $GLOBALS['TCA']['tt_content']['tx_vhscol_ctypes'][$cType->getValue()] = $cType->__toArray();
    }
}
