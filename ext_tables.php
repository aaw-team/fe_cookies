<?php
/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

defined('TYPO3_MODE') or die();

$bootstrap = function () {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_fecookies_domain_model_block');

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'AawTeam.FeCookies',
        'fecookies',
        'LLL:EXT:fe_cookies/Resources/Private/Language/locallang_db.xlf:plugin.fecookies.title',
        'EXT:fe_cookies/Resources/Public/Icons/Extension.svg'
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'AawTeam.FeCookies',
        'web',
        'fecookies',
        '',
        [
            'BackendModule' => 'index,noRootpage',
        ],
        [
            'access' => 'user,group',
            'icon' => 'EXT:fe_cookies/Resources/Public/Icons/Extension.svg',
            'labels' => 'LLL:EXT:fe_cookies/Resources/Private/Language/ModuleLabels.xlf',
        ]
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
mod.wizards.newContentElement.wizardItems.plugins {
    elements {
        minipoll_form {
            iconIdentifier = content-plugin-fecookies
            title = LLL:EXT:fe_cookies/Resources/Private/Language/locallang_db.xlf:plugin.fecookies.title
            description = LLL:EXT:fe_cookies/Resources/Private/Language/locallang_db.xlf:plugin.fecookies.description
            tt_content_defValues {
                CType = list
                list_type = fecookies_fecookies
            }
        }
    }
}
    ');
};
$bootstrap();
unset($bootstrap);