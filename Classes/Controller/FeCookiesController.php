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
     * @return bool
     */
    protected function shouldIncludeJs()
    {
        if ($this->settings['enableFrontendPlugin'] == -1) {
            return !(
                isset($GLOBALS['BE_USER'])
                && is_array($GLOBALS['BE_USER']->user)
                && $GLOBALS['BE_USER']->user['uid'] > 0);
        }
        return (bool)$this->settings['enableFrontendPlugin'];
    }

    /**
     *
     */
    public function indexAction()
    {
        // Add the js file
        if ($this->shouldIncludeJs()) {
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
        }

        // Add custom color definitions
        if (is_array($this->settings['styles'])) {
            $styles = '';
            if (is_array($this->settings['styles']['banner'])) {
                $styles .= $this->renderCssForProperty($this->settings['styles']['banner'], '#tx_fe_cookies-banner');
            }
            if (is_array($this->settings['styles']['acceptButton'])) {
                $styles .= $this->renderCssForProperty($this->settings['styles']['acceptButton'], '#tx_fe_cookies-banner #tx_fe_cookies-button-accept');
            }
            if (is_array($this->settings['styles']['closeButton'])) {
                $styles .= $this->renderCssForProperty($this->settings['styles']['closeButton'], '#tx_fe_cookies-banner #tx_fe_cookies-button-close', ['backgroundColor']);
                $styles .= $this->renderCssForProperty(['backgroundColor' => $this->settings['styles']['closeButton']['color']], '#tx_fe_cookies-banner #tx_fe_cookies-button-close span', ['backgroundColor']);
            }
            if (!empty($styles)) {
                $this->getPagerenderer()->addCssInlineBlock('fe_cookies_custom_styles', $styles);
            }
        }

        // Determine storage page(s)
        $storagePids = GeneralUtility::intExplode(
            ',',
            $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK)['persistence']['storagePid'],
            true
        );
        if (count($storagePids) == 1 && $storagePids[0] === 0) {
            // No storage page configured, use the rootpage(s) from the rootLine
            $storagePids = [];
            foreach ($this->getTypoScriptFrontendController()->rootLine as $page) {
                if ($page['is_siteroot']) {
                    $storagePids[] = (int)$page['uid'];
                }
            }
            if (!empty($storagePids)) {
                $querySettings = $this->blockRepository->createQuery()->getQuerySettings();
                $querySettings->setStoragePageIds($storagePids);
                $this->blockRepository->setDefaultQuerySettings($querySettings);
            }
        }

        // Compose the contents
        $this->view->assignMultiple([
            'blocks' => $this->blockRepository->findAll(),
        ]);
    }

    /**
     * @param array $settings
     * @param string $property
     * @throws \InvalidArgumentException
     * @return string
     */
    protected function renderCssForProperty(array $settings, $property, $allowedProperties = ['backgroundColor', 'color'])
    {
        if (!is_string($property) || empty($property)) {
            throw new \InvalidArgumentException('$property must be not empty string');
        }
        $style = $bg = $color = '';
        if (in_array('backgroundColor', $allowedProperties) && isset($settings['backgroundColor'])
            && is_string($settings['backgroundColor'])
            && $settings['backgroundColor'] != ''
        ) {
            $bg = $this->filterColorString($settings['backgroundColor']);
        }
        if (in_array('color', $allowedProperties) && isset($settings['color'])
            && is_string($settings['color'])
            && $settings['color'] != ''
        ) {
            $color = $this->filterColorString($settings['color']);
        }
        if (!empty($bg) || !empty($color)) {
            $style .= $property . '{';
            if (!empty($bg)) {
                $style .= 'background-color:' . $bg . ';';
            }
            if (!empty($color)) {
                $style .= 'color:' . $color . ';';
            }
            $style .= '}';
        }
        return $style;
    }

    /**
     * @param string $colorString
     * @return string
     */
    protected function filterColorString($colorString)
    {
        $return = '';
        if (preg_match('~^(#[a-f0-9]{3,6})|(rgba?\\s*\\(\\s*\\d{1,3}\\s*,\\s*\\d{1,3}\\s*,\\s*\\d{1,3}\\s*(?:,\\s*[\\d\\.]+)?\\s*\\))|([a-z\\-]+)$~i', $colorString)) {
            $return = $colorString;
        }
        return $return;
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
