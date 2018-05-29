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
            'BackendModule' => 'index,settings,infoBox',
        ],
        [
            'access' => 'user,group',
            'icon' => 'EXT:fe_cookies/Resources/Public/Icons/Extension.svg',
            'labels' => 'LLL:EXT:fe_cookies/Resources/Private/Language/ModuleLabels.xlf',
        ]
    );

    if (version_compare(TYPO3_version, '7', '<')) {
        // Show a convenient message in too old TYPO3 installations by just forcing another template root path
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript('fe_cookies', 'setup', '
module.tx_fecookies.view {
    templateRootPaths.0 = EXT:fe_cookies/Resources/Private/Templates/TooOldTYPO3/
}');
    }

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
mod.wizards.newContentElement.wizardItems.plugins {
    elements {
        fecookies_fecookies {
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
mod.fe_cookies {
    settingsManagement {
        enable = 0
        templateUid = 0
        templatePid =
        allowedConstantNames {
            0 = enableCloseButton
            1 = styles.*
        }
    }
}
    ');
};
$bootstrap();
unset($bootstrap);
