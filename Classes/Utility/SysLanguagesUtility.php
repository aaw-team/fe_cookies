<?php
namespace AawTeam\FeCookies\Utility;

/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * SysLanguagesUtility
 */
class SysLanguagesUtility
{
    /**
     * @return array
     */
    public static function getSysLanguageRecords($pageUid = 0, $includeDefaultLanguage = false)
    {
        $records = [];
        if ($includeDefaultLanguage) {
            $records[0] = static::getDefaultLanguage();
        }

        if (version_compare(TYPO3_version, '8.1', '>=')) {
            $queryBuilder = static::getQueryBuilderForTable('sys_language');
            $queryBuilder->getRestrictions()->removeAll()->add(
                GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction::class)
            )->add(
                GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\Query\Restriction\RootLevelRestriction::class)
            );
            $queryBuilder->select('*')
                ->from('sys_language')
                ->addOrderBy($GLOBALS['TCA']['sys_language']['ctrl']['sortby'])
            ;
            $rows = $queryBuilder->execute()->fetchAll();
        } else {
            $rows = static::getLegacyDatabaseConnection()->exec_SELECTgetRows(
                '*',
                'sys_language',
                'pid=0 ' . BackendUtility::deleteClause('sys_language'),
                '',
                $GLOBALS['TCA']['sys_language']['ctrl']['sortby']
            );
            if (!is_array($rows)) {
                $rows = [];
            }
        }
        foreach ($rows as $row) {
            $records[$row['uid']] = $row;
        }

        return $records;
    }

    /**
     * @return array
     */
    public static function getDefaultLanguage()
    {
        $defaultLanguageLabel = version_compare(TYPO3_version, '8', '>=')
            ? static::getLanguageService()->sL('LLL:EXT:lang/Resources/Private/Language/locallang_mod_web_list.xlf:defaultLanguage')
            : static::getLanguageService()->sL('LLL:EXT:lang/locallang_mod_web_list.xlf:defaultLanguage');
        $defaultLanguageFlag = '';
        $defaultLanguageIsocode = 'default';

        $tsconfig = BackendUtility::getPagesTSconfig(GeneralUtility::_GET('id'))['mod.']['SHARED.']['properties'] ?? null;
        if (is_array($tsconfig)) {
            if ($tsconfig['defaultLanguageLabel']) {
                $defaultLanguageLabel = $tsconfig['defaultLanguageLabel'] . ' (' . $defaultLanguageLabel . ')';
            }
            $defaultLanguageFlag = (string)$tsconfig['defaultLanguageFlag'];
        }

        return [
            'uid' => 0,
            'title' => $defaultLanguageLabel,
            'flag' => $defaultLanguageFlag,
            'language_isocode' => $defaultLanguageIsocode,
        ];
    }

    /**
     * @param string $isocode
     * @throws \InvalidArgumentException
     * @return array
     */
    public static function getSysLanguageRecordByIsocode($isocode)
    {
        if (!is_string($isocode) || empty($isocode)) {
            throw new \InvalidArgumentException('$isocode must be not empty string');
        }

        if ($isocode === 'default') {
            return [0 => self::getDefaultLanguage()];
        }

        $records = [];

        if (version_compare(TYPO3_version, '8.1', '>=')) {
            $queryBuilder = static::getQueryBuilderForTable('sys_language');
            $queryBuilder->getRestrictions()->removeAll()->add(
                GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction::class)
            )->add(
                GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\Query\Restriction\RootLevelRestriction::class)
            );
            $queryBuilder
                ->select('*')
                ->from('sys_language')
                ->where(
                    $queryBuilder->expr()->eq('language_isocode', $queryBuilder->createNamedParameter($isocode, \PDO::PARAM_STR))
                );
            $rows = $queryBuilder->execute()->fetchAll();
        } else {
            $rows = static::getLegacyDatabaseConnection()->exec_SELECTgetRows(
                '*',
                'sys_language',
                'pid=0 AND language_isocode=' . static::getLegacyDatabaseConnection()->fullQuoteStr($isocode, 'syas_language') . BackendUtility::deleteClause('sys_language'),
                '',
                $GLOBALS['TCA']['sys_language']['ctrl']['sortby']
            );
            if (!is_array($rows)) {
                $rows = [];
            }
        }

        foreach ($rows as $row) {
            $records[$row['uid']] = $row;
        }

        return $records;
    }

    /**
     * @param string $table
     * @return \TYPO3\CMS\Core\Database\Query\QueryBuilder
     */
    protected static function getQueryBuilderForTable($table)
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
    }

    /**
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected static function getLegacyDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected static function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }
}
