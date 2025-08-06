<?php

declare(strict_types=1);

namespace TRAW\VhsCol\ViewHelpers\Date;

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
    public function initializeArguments(): void
    {
        $this->registerArgument('start', 'integer', 'start timestamp', true);
        $this->registerArgument('end', 'integer', 'end timestamp', true);
        $this->registerArgument('dateFormat', 'string', 'format', false);
        $this->registerArgument('dateFormatDay', 'string', 'format', false);
        $this->registerArgument('dateFormatMonth', 'string', 'format', false);
        $this->registerArgument('sep', 'string', 'format', false);
    }

    #[\Override]
    public function render(): string
    {
        $arguments = $this->arguments;

        $startDate = new \DateTime();
        $endDate = new \DateTime();

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
                $startFormat = $interval->y > 0 || $startDate->format('Y') !== $endDate->format('Y') ? $formats['date'] : $formats['m'];
            }

            return $startDate->format($startFormat)
                . ' ' . $formats['sep'] . ' '
                . $endDate->format($formats['date']);
        }
        return $startDate->format($formats['date']);
    }

    protected function getDateFormats(): array
    {
        return ['date' => empty($this->arguments['dateFormat'])
            ? LocalizationUtility::translate('dateFormat', 'VhsCol')
            : $this->arguments['dateFormat'], 'd' => empty($this->arguments['dateFormatDay'])
            ? LocalizationUtility::translate('dateFormatDay', 'VhsCol')
            : $this->arguments['dateFormatDay'], 'm' => empty($this->arguments['dateFormatMonth'])
            ? LocalizationUtility::translate('dateFormatMonth', 'VhsCol')
            : $this->arguments['dateFormatMonth'], 'sep' => empty($this->arguments['sep'])
            ? LocalizationUtility::translate('dateFormatSeperator', 'VhsCol')
            : $this->arguments['sep']];
    }
}
