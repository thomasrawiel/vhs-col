<?php
declare(strict_types=1);
namespace TRAW\VhsCol\Utility;

use Doctrine\DBAL\Exception;
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
     * @throws Exception
     */
    public static function getContentAttributeByUid(int $uid, string $attribute ='CType'): mixed
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
        return $queryBuilder->select($attribute)
            ->from('tt_content')
            ->where($queryBuilder->expr()->eq(
                'uid',
                $queryBuilder->createNamedParameter($uid, \Doctrine\DBAL\ParameterType::INTEGER)
            ))->executeQuery()->fetchOne();
    }
}
