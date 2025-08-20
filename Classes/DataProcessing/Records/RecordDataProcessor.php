<?php

namespace TRAW\VhsCol\DataProcessing\Records;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentDataProcessor;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

final class RecordDataProcessor implements DataProcessorInterface
{
    public function __construct(private readonly ContentDataProcessor $contentDataProcessor)
    {
    }

    public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData): array
    {
        if (isset($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }

        $fieldName = $cObj->stdWrapValue('fieldName', $processorConfiguration, 'pages');

        $recordList = [];
        //grouped by table
        $uidList = [];
        //keep the order of records from the pages field
        $uidSequence = [];

        $recordData = GeneralUtility::trimExplode(',', $processedData['data'][$fieldName], true);
        if (empty($recordData)) {
            return $processedData;
        }

        foreach ($recordData as $data) {
            [$table, $uid] = preg_split('~.*\K_~', $data);
            $uidList[$table][] = $uid;
            $uidSequence[] = $uid;
        }

        unset($processorConfiguration['table'], $processorConfiguration['table.']);

        foreach ($uidList as $tableName => $uids) {
            $processorConfiguration['uidInList'] = implode(',', $uids);
            $processorConfiguration['table'] = $tableName;

            $records = $cObj->getRecords($tableName, $processorConfiguration);
            if (!empty($records)) {
                foreach ($this->processRecords($records, $cObj, $tableName, $processorConfiguration) as $record) {
                    $recordList[] = $record;
                }
            }
        }

        $targetVariableName = $cObj->stdWrapValue('as', $processorConfiguration, 'record');
        if (!empty($recordList)) {
            $processedData[$targetVariableName] = $this->sortRecordListByUidList($recordList, $uidSequence);
        }

        return $processedData;
    }

    protected function sortRecordListByUidList(array $recordList, array $uidList): array
    {
        $orderMap = array_flip($uidList); // [13 => 0, 7 => 1, 42 => 2]

        usort($recordList, static function (array $a, array $b) use ($orderMap): int {
            return ($orderMap[$a['data']['uid']] ?? PHP_INT_MAX)
                <=> ($orderMap[$b['data']['uid']] ?? PHP_INT_MAX);
        });

        return $recordList;
    }

    protected function processRecords(array $records, ContentObjectRenderer $cObj, string $tableName, array $processorConfiguration)
    {
        $processedRecordVariables = [];

        foreach ($records as $key => $record) {
            $currentRecord = $record;
            if ($tableName === 'pages' && $record['uid'] && $record['mount_pid'] > 0 && $record['mount_pid_ol'] === 1) {
                $processorConfiguration['uidInList'] = $record['mount_pid'];
                $mountedRecords = $cObj->getRecords($tableName, $processorConfiguration);

                $currentRecord = $mountedRecords[0];
            }
            /** @var ContentObjectRenderer $recordContentObjectRenderer */
            $recordContentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
            $recordContentObjectRenderer->start($currentRecord, $tableName);
            $processedRecordVariables[$key] = ['data' => $currentRecord, 'context' => $tableName];
            $processedRecordVariables[$key] = $this->contentDataProcessor->process($recordContentObjectRenderer, $processorConfiguration, $processedRecordVariables[$key]);

            if ($currentRecord['uid'] !== $record['uid']) {
                $processedRecordVariables[$key]['mount'] = $record['uid'];
            }
        }

        return $processedRecordVariables;
    }
}
