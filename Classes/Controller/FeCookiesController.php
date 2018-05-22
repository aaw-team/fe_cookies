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
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Core\Messaging\AbstractMessage;

/**
 * FeCookiesController
 */
class FeCookiesController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
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
        // Add the css file
        $this->getPagerenderer()->addCssFile(
            $this->getTypoScriptFrontendController()->tmpl->getFileName('EXT:fe_cookies/Resources/Public/Css/DefaultStyle.css')
        );
        // Add the js file
        $this->getPagerenderer()->addJsFooterLibrary(
            'fe_cookies',
            $this->getTypoScriptFrontendController()->tmpl->getFileName('EXT:fe_cookies/Resources/Public/JavaScript/FeCookies.js')
        );

        // Determine rootpage
        $rootPage = null;
        foreach ($this->getTypoScriptFrontendController()->rootLine as $page) {
            if ($page['is_siteroot']) {
                $rootPage = $page;
                break;
            }
        }
        if (!$rootPage) {
            // @todo: error mesaging
            return 'Error: cannot determine rootpage';
        }

        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface $querySettings */
        $querySettings = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface::class);
        $querySettings->setStoragePageIds([$rootPage['uid']]);
        $this->blockRepository->setDefaultQuerySettings($querySettings);

        // Compose the contents
        $this->view->assignMultiple([
            'rootPage' => $rootPage,
            'blocks' => $this->blockRepository->findAll(),
        ]);
    }

    /**
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }

    /**
     * @return \TYPO3\CMS\Core\Page\PageRenderer
     */
    protected function getPagerenderer()
    {
        if (version_compare(TYPO3_version, '7', '<')) {
            return $this->getTypoScriptFrontendController()->getPageRenderer();
        }
        return GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);
    }
}
