<?php
namespace AawTeam\FeCookies\Controller;

/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

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
        // Add the js file
        $this->getPagerenderer()->addJsFooterFile(
            $this->getTypoScriptFrontendController()->tmpl->getFileName('EXT:fe_cookies/Resources/Public/JavaScript/CookieBannerHandler.js'),
            'text/javascript',
            false,                                                                    // $compress
            false,                                                                    // $forceOnTop
            '',                                                                       // $allWrap
            false,                                                                    // $excludeFromConcatenation
            '|',                                                                      // $splitChar
            false,                                                                    // $async
            // SRI hash generated with: php -r 'print "sha384-" . base64_encode(hash_file("sha384", "Resources/Public/JavaScript/CookieBannerHandler.js", true)) . PHP_EOL;'
            'sha384-1VuV3tkg3iJ5ine3LmrM+AEUw/fY8Hm4RQBAH2QpqNIS85/MYWRrL1gATeZI2mRg' // $integrity
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
