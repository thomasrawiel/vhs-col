<?php

namespace TRAW\VhsCol\Utility;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class DatabaseUtility
 * @package TRAW\VhsCol\Utility
 */
class DatabaseUtility
{
    /**
     * @param int    $uid
     * @param string $attribute
     *
     * @return false|mixed
     * @throws \Doctrine\DBAL\Exception
     */
    public static function getContentAttributeByUid(int $uid, string $attribute ='CType') {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
        return $queryBuilder->select($attribute)
            ->from('tt_content')
            ->where($queryBuilder->expr()->eq(
                'uid',
                $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
            ))->execute()->fetchOne();
    }
}