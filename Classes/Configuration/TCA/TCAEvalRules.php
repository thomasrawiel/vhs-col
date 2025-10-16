<?php
declare(strict_types=1);

namespace TRAW\VhsCol\Configuration\TCA;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class TCAEvalRules
 *
 * @see https://docs.typo3.org/m/typo3/reference-tca/main/en-us/ColumnsConfig/Type/Input/#confval-input-eval
 *
 * Usage example:
 *  Add trim rule to tt-content header
 * \TRAW\VhsCol\Configuration\TCA\TCAEvalRules::addEvalRules('tt_content', 'header', ['trim']);
 *      result 'eval' => 'trim'
 *
 * Add alphanum and unique rule to the existing trim rule for pages title
 * \TRAW\VhsCol\Configuration\TCA\TCAEvalRules::addEvalRules('pages', 'title', ['alphanum', 'unique]);
 *      result 'eval' => 'trim,alphanum,unique'
 *
 */
final class TCAEvalRules
{
    /**
     * Add eval rules
     *
     * @param string $table
     * @param string $fieldName
     * @param array  $evalValues
     *
     * @return void
     */
    public static function addEvalRules(string $table, string $fieldName, array $evalValues): void
    {
        if (empty($GLOBALS['TCA'][$table]['columns'][$fieldName]['config'])
            || !is_array($GLOBALS['TCA'][$table]['columns'][$fieldName]['config'])
        ) {
            return;
        }

        $existingEval = $GLOBALS['TCA'][$table]['columns'][$fieldName]['config']['eval'] ?? '';
        $eval = array_filter(GeneralUtility::trimExplode(',', (string)$existingEval, true));
        $eval = array_unique(array_merge($eval, $evalValues));
        $eval = array_filter($eval, static fn(string $value): bool => self::evalRuleIsValid($value));

        if(!empty($eval)){
            $GLOBALS['TCA'][$table]['columns'][$fieldName]['config']['eval'] = implode(',', $eval);
        }
    }

    /**
     * Remove eval rules
     *
     * @param string $table
     * @param string $fieldName
     * @param array  $evalValues
     *
     * @return void
     */
    public static function removeEvalRules(string $table, string $fieldName, array $evalValues): void
    {
        if (empty($GLOBALS['TCA'][$table]['columns'][$fieldName]['config'])
            || !is_array($GLOBALS['TCA'][$table]['columns'][$fieldName]['config'])
        ) {
            return;
        }

        $existingEval = $GLOBALS['TCA'][$table]['columns'][$fieldName]['config']['eval'] ?? '';
        $eval = array_filter(GeneralUtility::trimExplode(',', (string)$existingEval, true));
        $eval = array_values(array_filter($eval, static fn(string $value): bool => !in_array($value, $evalValues, true)));

        if(!empty($eval)){
            $GLOBALS['TCA'][$table]['columns'][$fieldName]['config']['eval'] = implode(',', $eval);
        }
    }

    /**
     * Set eval rules, regardless of existing rules
     *
     * @param string $table
     * @param string $fieldName
     * @param array  $evalValues
     *
     * @return void
     */
    public static function setEvalRules(string $table, string $fieldName, array $evalValues): void
    {
        if (empty($GLOBALS['TCA'][$table]['columns'][$fieldName]['config'])
            || !is_array($GLOBALS['TCA'][$table]['columns'][$fieldName]['config'])
        ) {
            return;
        }
        $eval = array_unique(array_filter($eval, static fn(string $value): bool => self::evalRuleIsValid($value)));

        if(!empty($eval)){
            $GLOBALS['TCA'][$table]['columns'][$fieldName]['config']['eval'] = implode(',', $eval);
        }
    }

    /**
     * Check if eval rules are valid
     *
     * @param string|null $eval
     *
     * @return bool
     */
    private static function evalRuleIsValid(?string $eval): bool
    {
        $allowedEvalValues = ['alpha', 'alphanum', 'alphanum_x', 'domainname', 'is_in', 'lower', 'md5', 'nospace', 'num', 'trim', 'unique', 'uniqueInPid', 'upper', 'year'];

        if (in_array($eval, $allowedEvalValues)) {
            return true;
        }

        //Custom eval rules
        if (class_exists($eval) && isset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals'][$eval])) {
            return true;
        }

        return false;
    }
}
