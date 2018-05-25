<?php
namespace AawTeam\FeCookies;

/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

defined('TYPO3_MODE') or die();

$bootstrap = function () {
    $eidController = GeneralUtility::makeInstance(\AawTeam\FeCookies\Controller\EidController::class);
    $eidController->legacyAction();
};
$bootstrap();
unset($bootstrap);
