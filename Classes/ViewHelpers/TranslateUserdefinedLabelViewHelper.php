<?php
namespace AawTeam\FeCookies\ViewHelpers;

/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use AawTeam\FeCookies\Utility\LocalizationUtility;

/**
 * TranslateUserdefinedLabelViewHelper
 */
class TranslateUserdefinedLabelViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * {@inheritDoc}
     * @see \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper::initializeArguments()
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
                if (isset($GLOBALS['TSFE']->config['config']['language'])) {
                    $language = $GLOBALS['TSFE']->config['config']['language'];
                }
            } elseif (!empty($GLOBALS['BE_USER']->uc['lang'])) {
                $language = $GLOBALS['BE_USER']->uc['lang'];
            } elseif (!empty($GLOBALS['LANG']->lang)) {
                $language = $GLOBALS['LANG']->lang;
            }
        }
        return $language;
    }
}
