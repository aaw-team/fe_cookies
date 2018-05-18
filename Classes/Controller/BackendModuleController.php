<?php
namespace AawTeam\FeCookies\Controller;

/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use AawTeam\FeCookies\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Messaging\AbstractMessage;

/**
 * BackendModuleController
 */
class BackendModuleController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * @var \AawTeam\FeCookies\Domain\Repository\BlockRepository
     * @inject
     */
    protected $blockRepository;

    /**
     *
     */
    public function indexAction()
    {
        if (!$this->isRootpage()) {
            $this->forward('noRootpage');
        }

        $pageUid = (int)GeneralUtility::_GP('id');

        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface $querySettings */
        $querySettings = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface::class);
        $querySettings->setStoragePageIds([$pageUid]);
        $querySettings->setIgnoreEnableFields(true);
        $this->blockRepository->setDefaultQuerySettings($querySettings);

        $this->view->assignMultiple([
            'pageUid' => $pageUid,
            'blocks' => $this->blockRepository->findAll(),
        ]);
    }




//     /**
//      * {@inheritDoc}
//      * @see \TYPO3\CMS\Extbase\Mvc\Controller\AbstractController::addFlashMessage()
//      */
//     public function addFlashMessage($messageBody, $messageTitle = '', $severity = \TYPO3\CMS\Core\Messaging\AbstractMessage::OK, $storeInSession = true)
//     {
//         return parent::addFlashMessage(
//             LocalizationUtility::translate($messageBody),
//             LocalizationUtility::translate($messageTitle),
//             $severity,
//             $storeInSession
//         );
//     }

    /**
     *
     */
    public function noRootpageAction()
    {
    }

    /**
     * @return boolean
     */
    protected function isRootpage()
    {
        $pageId = (int)GeneralUtility::_GP('id');
        $page = BackendUtility::getRecord('pages', $pageId);
        if (is_array($page)) {
            return (bool)$page['is_siteroot'];
        }
        return false;
    }

//     /**
//      * @return \TYPO3\CMS\Core\Database\DatabaseConnection
//      */
//     protected function getLegacyDatabaseConnection()
//     {
//         return $GLOBALS['TYPO3_DB'];
//     }

//     /**
//      * @return \TYPO3\CMS\Core\Database\Connection
//      */
//     protected function getConnectionForTable(string $tableName)
//     {
//         return GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)->getConnectionForTable($tableName);
//     }
}
