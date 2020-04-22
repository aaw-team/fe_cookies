<?php
namespace AawTeam\FeCookies\Hook;

/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use AawTeam\FeCookies\Configuration\Configuration;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Resource\FilePathSanitizer;

/**
 * PageRendererHook
 */
class PageRendererHook
{
    /**
     * Add the JS cookie configuration, when the fe_cookies JS API is
     * registered in PageRenderer.
     *
     * @param array $params
     * @param PageRenderer $pageRenderer
     */
    public function run(array $params, PageRenderer $pageRenderer)
    {
        if (!$this->isInFrontendMode()) {
            return;
        }

        $section = $this->isFeCookiesJsRegistered($params);
        if ($section !== false) {
            $name = 'fe_cookies_configuration';
            $file = GeneralUtility::makeInstance(Configuration::class)->getJsConfigurationFile();
            $type = 'text/javascript';
            $compress = false;
            $forceOnTop = true;
            $allWrap = '';
            $excludeFromConcatenation = false;
            $splitChar = '|';
            $async = false;
            $integrity = 'sha384-' . base64_encode(hash_file("sha384", GeneralUtility::getFileAbsFileName($file), true));

            if ($section === PageRenderer::PART_HEADER) {
                $pageRenderer->addJsLibrary(
                    $name,
                    $file,
                    $type,
                    $compress,
                    $forceOnTop,
                    $allWrap,
                    $excludeFromConcatenation,
                    $splitChar,
                    $async,
                    $integrity
                );
            } else {
                $pageRenderer->addJsFooterLibrary(
                    $name,
                    $file,
                    $type,
                    $compress,
                    $forceOnTop,
                    $allWrap,
                    $excludeFromConcatenation,
                    $splitChar,
                    $async,
                    $integrity
                );
            }
        }
    }

    /**
     * Returns the section (PageRenderer::PART_HEADER or
     * PageRenderer::PART_FOOTER) where the script is registered, or
     * false, if it is not.
     *
     * @param array $params
     * @return int|boolean
     */
    protected function isFeCookiesJsRegistered(array $params)
    {
        if (isset($params['jsLibs']['fe_cookies'])) {
            return $params['jsLibs']['fe_cookies']['section'];
        } elseif (isset($params['jsFooterLibs']['fe_cookies'])) {
            return $params['jsFooterLibs']['fe_cookies']['section'];
        }

        $filename = GeneralUtility::makeInstance(FilePathSanitizer::class)->sanitize('EXT:fe_cookies/Resources/Public/JavaScript/FeCookies.js');
        if (isset($params['jsFiles'][$filename])) {
            return $params['jsFiles'][$filename]['section'];
        } elseif (isset($params['jsFooterFiles'][$filename])) {
            return $params['jsFooterFiles'][$filename]['section'];
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function isInFrontendMode()
    {
        return TYPO3_MODE === 'FE';
    }

    /**
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }
}
