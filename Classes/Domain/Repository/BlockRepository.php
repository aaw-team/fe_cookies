<?php
namespace AawTeam\FeCookies\Domain\Repository;

/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;

/**
 * BlockRepository
 */
class BlockRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * @var array
     */
    protected $defaultOrderings = [
        'sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
    ];

    /**
     * @return array
     */
    public function findAllForBackendList()
    {
        $rows = $this->findRecordsByL18nParent();

        $translations = [];
        foreach ($rows as $position => $row) {
            $translations[$position + 1] = $this->findRecordsByL18nParent((int)$row['uid']);
        }

        $positionShift = 0;
        foreach ($translations as $position => $translationRows) {
            if (empty($translationRows)) {
                continue;
            }
            // Insert all translations after their l18n_parent record
            $part1 = array_slice($rows, 0, $position + $positionShift);
            $part2 = array_slice($rows, $position + $positionShift);
            $rows = array_merge($part1, $translationRows, $part2);

            $positionShift += count($translationRows);
        }

        return $rows;
    }

    /**
     * @param int $l18nParent
     * @return array
     */
    protected function findRecordsByL18nParent($l18nParent = 0)
    {
        if (!is_int($l18nParent) || $l18nParent < 0) {
            throw new \InvalidArgumentException('$l18nParent must be positive integer or zero');
        }

        $pids = $this->createQuery()->getQuerySettings()->getStoragePageIds();

        if (version_compare(TYPO3_version, '8.2', '<')) {
            $orderings = [];
            foreach ($this->defaultOrderings as $field => $orderDir) {
                $orderings[] = '`' . $this->getDatabaseTableName() . '`.`' . $this->getLegacyDatabaseConnection()->quoteStr($field, $this->getDatabaseTableName()) . '` ' . ($orderDir === \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING ? 'DESC' : 'ASC');
            }
            $rows = $this->getLegacyDatabaseConnection()->exec_SELECTgetRows(
                '*',
                $this->getDatabaseTableName(),
                'pid IN (' . implode(',', $pids) . ') AND l18n_parent=' . $l18nParent . ' ' . BackendUtility::deleteClause($this->getDatabaseTableName()),
                '',
                implode(', ', $orderings)
            );
            if (!is_array($rows)) {
                $rows = [];
            }
        } else {
            $queryBuilder = $this->getConnectionForTable($this->getDatabaseTableName())->createQueryBuilder();
            $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
            $queryBuilder
                ->select('*')
                ->from($this->getDatabaseTableName())
                ->where(
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->in('pid', $pids),
                        $queryBuilder->expr()->eq('l18n_parent', $l18nParent)
                    )
                );
            foreach ($this->defaultOrderings as $field => $orderDir) {
                $queryBuilder->addOrderBy($field, $orderDir);
            }
            $rows = $queryBuilder->execute()->fetchAll();
        }
        return $rows;
    }

    /**
     * @return string
     */
    protected function getDatabaseTableName()
    {
        static $tableName = null;
        if ($tableName === null) {
            $tableName = $this->objectManager->get(DataMapper::class)->convertClassNameToTableName($this->objectType);
        }
        return $tableName;
    }

    /**
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getLegacyDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * @return \TYPO3\CMS\Core\Database\Connection
     */
    protected function getConnectionForTable($tableName)
    {
        return GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)->getConnectionForTable($tableName);
    }
}
