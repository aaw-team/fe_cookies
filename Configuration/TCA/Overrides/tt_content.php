<?php
/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

// Register the frontend plugin
$extensionName = 'FeCookies';
// Prepend the vendor name for TYPO3 versions below 10.1
if (version_compare(TYPO3_version, '10.1', '<')) {
    $extensionName = 'AawTeam.' . $extensionName;
}
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    $extensionName,
    'fecookies',
    'LLL:EXT:fe_cookies/Resources/Private/Language/locallang_db.xlf:plugin.fecookies.title',
    'EXT:fe_cookies/Resources/Public/Icons/Extension.svg'
);
