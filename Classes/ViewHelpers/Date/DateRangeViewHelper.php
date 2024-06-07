<?php

namespace TRAW\VhsCol\ViewHelpers\Date;

use ApacheSolrForTypo3\Solr\System\Data\DateTime;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class DateRangeViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        $this->registerArgument('start', 'int', 'start timestamp', true);
        $this->registerArgument('end', 'int', 'end timestamp', true);
        $this->registerArgument('dateformat', 'string', 'format', false);
        $this->registerArgument('dateformatDay', 'string', 'format', false);
        $this->registerArgument('dateformatMonth', 'string', 'format', false);
        $this->registerArgument('sep', 'string', 'format', false);
    }

    public function render()
    {
        $startDate = new DateTime();
        $startDate->setTimestamp($this->arguments['start']);
        $endDate = new DateTime();
        $endDate->setTimestamp($this->arguments['end']);
        $formats = $this->getDateFormats();
        $interval = $startDate->diff($endDate);

        if ($interval->d > 0) {
            $startFormat = $formats['d'];
            if ($interval->m > 0 || $startDate->format('n') !== $endDate->format('n')) {
                if ($interval->y > 0 || $startDate->format('Y') !== $endDate->format('Y')) {
                    $startFormat = $formats['date'];
                } else {
                    $startFormat = $formats['m'];
                }
            }
            return $startDate->format($startFormat)
                . ' '.$formats['sep'].' '
                . $endDate->format($formats['date']);
        } else {
            return $startDate->format($formats['date']);
        }
    }

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