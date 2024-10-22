<?php
declare(strict_types=1);
namespace TRAW\VhsCol\ViewHelpers\Date;

use DateTime;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class DateRangeViewHelper
 *
 * Usage:
 * <vcol:date.dateRange start="{data.dateRangeStart}" end="{data.dateRangeEnd}"/>
 */
class DateRangeViewHelper extends AbstractViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('start', 'integer', 'start timestamp', true);
        $this->registerArgument('end', 'integer', 'end timestamp', true);
        $this->registerArgument('dateFormat', 'string', 'format', false);
        $this->registerArgument('dateFormatDay', 'string', 'format', false);
        $this->registerArgument('dateFormatMonth', 'string', 'format', false);
        $this->registerArgument('sep', 'string', 'format', false);
    }

    /**
     * @return string
     */
    public function render()
    {
        $arguments = $this->arguments;

        $startDate = new DateTime();
        $endDate = new DateTime();

        if (($arguments['start'] ?? 0) > 0) {
            $startDate->setTimestamp($arguments['start']);
        }
        if (($arguments['end'] ?? 0) > 0) {
            $endDate->setTimestamp($arguments['end']);
        }
        //fallbacks:
        //if no start date is given, assume same day as end
        if ($arguments['start'] === 0) {
            $startDate->setTimestamp($endDate->getTimestamp());
        }
        //if no end date is given, assume same day as start
        if ($arguments['end'] === 0) {
            $endDate->setTimestamp($startDate->getTimestamp());
        }

        $formats = $this->getDateFormats();
        $interval = $startDate->diff($endDate);

        if ($interval !== false && $interval->d > 0) {
            $startFormat = $formats['d'];
            if ($interval->y > 0 || $startDate->format('Y') !== $endDate->format('Y')) {
                $startFormat = $formats['date'];
            }
            if ($interval->m > 0 || $startDate->format('n') !== $endDate->format('n')) {
                if ($interval->y > 0 || $startDate->format('Y') !== $endDate->format('Y')) {
                    $startFormat = $formats['date'];
                } else {
                    $startFormat = $formats['m'];
                }
            }
            return $startDate->format($startFormat)
                . ' ' . $formats['sep'] . ' '
                . $endDate->format($formats['date']);
        } else {
            return $startDate->format($formats['date']);
        }
    }

    /**
     * @return array
     */
    protected function getDateFormats(): array
    {
        $formats = [];
        $formats['date'] = !empty($this->arguments['dateFormat'])
            ? $this->arguments['dateFormat']
            : LocalizationUtility::translate('dateFormat', 'VhsCol');
        $formats['d'] = !empty($this->arguments['dateFormatDay'])
            ? $this->arguments['dateFormatDay']
            : LocalizationUtility::translate('dateFormatDay', 'VhsCol');
        $formats['m'] = !empty($this->arguments['dateFormatMonth'])
            ? $this->arguments['dateFormatMonth']
            : LocalizationUtility::translate('dateFormatMonth', 'VhsCol');
        $formats['sep'] = !empty($this->arguments['sep'])
            ? $this->arguments['sep']
            : LocalizationUtility::translate('dateFormatSeperator', 'VhsCol');
        return $formats;
    }
}
