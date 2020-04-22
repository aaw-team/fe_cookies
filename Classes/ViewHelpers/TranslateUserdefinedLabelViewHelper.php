<?php
namespace AawTeam\FeCookies\ViewHelpers;

/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use AawTeam\FeCookies\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * TranslateUserdefinedLabelViewHelper
 */
class TranslateUserdefinedLabelViewHelper extends AbstractViewHelper
{
    /**
     * {@inheritDoc}
     * @see AbstractViewHelper::initializeArguments()
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('key', 'string', 'Translation Key');
    }

    /**
     * @return string|null
     */
    public function render()
    {
        $key = $this->arguments['key'];
        $translated = LocalizationUtility::translateUserdefinedLabel($key, $this->getLanguage());
        if ($translated === null) {
            $translated = LocalizationUtility::translate('LLL:EXT:fe_cookies/Resources/Private/Language/userdefinedLabels.xlf:' . $key);
        }
        return $translated;
    }

    /**
     * @return string
     */
    protected function getLanguage()
    {
        static $language = null;
        if ($language === null) {
            $language = 'default';
            if (TYPO3_MODE === 'FE') {
                $siteLanguage = $GLOBALS['TYPO3_REQUEST']->getAttribute('language');
                if ($siteLanguage instanceof SiteLanguage) {
                    $language = $siteLanguage->getTypo3Language();
                }
            }
        }
        return $language;
    }
}
