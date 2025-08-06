<?php

declare(strict_types=1);

namespace TRAW\VhsCol\Helpers;

use TRAW\VhsCol\Configuration\TCA\CType;
use TRAW\VhsCol\Information\Typo3Version;

/**
 * Generates PageTS configuration to register or remove custom content elements
 * from the content element wizard depending on TYPO3 version and CType settings.
 */
final class PageTsGenerator
{
    /**
     * Generates the PageTS for the given CType objects.
     *
     * @param array<CType> $cTypes Array of CType objects
     *
     * @return string PageTS configuration string
     */
    public static function generate(array $cTypes = []): string
    {
        $wizardItems = [];
        $removeItems = [];
        $headers = [];

        if ($cTypes === []) {
            $cTypes = $GLOBALS['TCA']['tt_content']['tx_vhscol_ctypes'] ?? [];
        }

        foreach ($cTypes as $cType) {
            if (($cType instanceof CType || is_array($cType)) && !empty($cType)) {
                if (is_array($cType)) {
                    $cType = new CType($cType);
                }
            } else {
                throw new \Exception('CType must be an instance of ' . \TRAW\VhsCol\Configuration\TCA\CType::class . ' or array', 9552057115);
            }

            $value = $cType->getValue();
            $label = $cType->getLabel();
            $description = $cType->getDescription() ?? '';
            $iconIdentifier = $cType->getIconIdentifier() ?? 'content-special';
            $group = $cType->getGroup() ?? 'common';
            $register = $cType->getRegisterInNewContentElementWizard();
            $defaultValues = $cType->getDefaultValues();

            if ($register === false && self::isTypo3v13OrHigher()) {
                $removeItems[$group][] = $value;
                continue;
            }

            if (!self::isTypo3v13OrHigher()) {
                $wizardItems[$group][$value] = self::renderWizardConfig(
                    $value,
                    $label,
                    $description,
                    $iconIdentifier,
                    $defaultValues,
                );
            }

            // Set group header once
            if (!isset($headers[$group])) {
                if (isset($GLOBALS['TCA']['tt_content']['columns']['CType']['config']['itemGroups'][$group])) {
                    $headers[$group] = $GLOBALS['TCA']['tt_content']['columns']['CType']['config']['itemGroups'][$group];
                } else {
                    $headers[$group] = $group; // Fallback: use group key as label
                }
            }
        }

        return self::renderPageTs($wizardItems, $removeItems, $headers);
    }

    /**
     * Checks if current TYPO3 version is 13 or higher.
     */
    private static function isTypo3v13OrHigher(): bool
    {
        return Typo3Version::getTypo3MajorVersion() >= 13;
    }

    /**
     * Renders configuration for a single wizard element.
     */
    private static function renderWizardConfig(
        string $value,
        string $label,
        string $description,
        string $iconIdentifier,
        array $defaultValues = [],
    ): string {

        $defValueLines = 'CType = ' . $value;
        foreach ($defaultValues as $key => $val) {
            $defValueLines .= LF . sprintf('            %s = %s', $key, $val) . LF;
        }

        $defValueLines = rtrim($defValueLines, LF);

        return <<<TS
            {$value} {
                iconIdentifier = {$iconIdentifier}
                title = {$label}
                description = {$description}
                tt_content_defValues {
                    {$defValueLines}
                }
            }
        TS;
    }

    /**
     * Combines all TS fragments into a complete PageTS output.
     *
     * @param array<string, array<string, string>> $wizardItems
     * @param array<string, array<string>>         $removeItems
     * @param array<string, string>                $headers
     */
    private static function renderPageTs(
        array $wizardItems,
        array $removeItems,
        array $headers
    ): string {
        if ($wizardItems === [] && $removeItems === []) {
            return '';
        }

        $tsLines = $wizardItems === [] ? [] : ['mod.wizards.newContentElement.wizardItems {'];

        foreach ($wizardItems as $group => $elements) {
            $tsLines[] = sprintf('  %s {', $group);

            if (isset($headers[$group])) {
                $tsLines[] = '    header = ' . $headers[$group];
            }

            $tsLines[] = '    elements {';
            foreach ($elements as $elementConfig) {
                $tsLines[] = self::indentBlock($elementConfig, 6);
            }

            $tsLines[] = '    }';
            $tsLines[] = sprintf('    show := addToList(%s)', implode(',', array_keys($elements)));
            $tsLines[] = '  }';
        }

        if ($wizardItems !== []) {
            $tsLines[] = '}';
        }

        foreach ($removeItems as $group => $values) {
            $tsLines[] = sprintf(
                'mod.wizards.newContentElement.wizardItems.%s.removeItems := addToList(%s)',
                $group,
                implode(',', $values)
            );
        }

        return implode(PHP_EOL, $tsLines) . PHP_EOL;
    }

    /**
     * Indents a multiline string block.
     */
    private static function indentBlock(string $block, int $spaces): string
    {
        $prefix = str_repeat(' ', $spaces);
        return implode(PHP_EOL, array_map(
            static fn(string $line): string => $prefix . $line,
            explode(PHP_EOL, $block)
        ));
    }
}
