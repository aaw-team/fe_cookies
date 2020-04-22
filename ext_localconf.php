<?php
/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

defined('TYPO3_MODE') or die();

$bootstrap = function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'AawTeam.FeCookies',
        'fecookies',
        [
            'FeCookies' => 'index',
        ]
    );

    // Configuration cache
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['fecookies_configuration'] = [
        'backend' => \TYPO3\CMS\Core\Cache\Backend\SimpleFileBackend::class,
        'frontend' => \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class,
        'options' => [],
        'groups' => ['system'],
    ];

    // Register PageRenderer hook
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][] = \AawTeam\FeCookies\Hook\PageRendererHook::class . '->run';

    // Register eID
    $GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['fecookies'] = \AawTeam\FeCookies\Controller\EidController::class . '::mainAction';

    /** @var \TYPO3\CMS\Core\Imaging\IconRegistry $iconRegistry */
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    $iconRegistry->registerIcon(
        'content-plugin-fecookies',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        [
            'source' => 'EXT:fe_cookies/Resources/Public/Icons/Extension.svg'
        ]
    );
    $iconRegistry->registerIcon(
        'fecookies-block',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        [
            'source' => 'EXT:fe_cookies/Resources/Public/Icons/Block.svg'
        ]
    );

    // Register the default TypoScript
    $typoScript = '/**
 * Completely disable the plugin, when not enabled
 */
[hideFrontendPlugin({$plugin.tx_fecookies.settings.enableFrontendPlugin})]
tt_content.list.20.fecookies_fecookies >
[global]';
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript('fe_cookies', 'setup', $typoScript, 'defaultContentRendering');
};
$bootstrap();
unset($bootstrap);
