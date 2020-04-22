<?php
namespace AawTeam\FeCookies\ViewHelpers\Form;

/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use AawTeam\FeCookies\Utility\LocalizationUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * UserdefinedLanguageLabelViewHelper
 */
class UserdefinedLanguageLabelViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Form\AbstractFormFieldViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'input';

    /**
     * {@inheritDoc}
     * @see \TYPO3\CMS\Fluid\ViewHelpers\Form\AbstractFormFieldViewHelper::initializeArguments()
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerArgument('label', 'string', 'The label', true);
        $this->registerArgument('language', 'string|array', 'The language', true);
    }

    /**
     * @throws \InvalidArgumentException
     * @return string
     */
    public function render()
    {
        $label = $this->arguments['label'];
        $language = $this->arguments['language'];
        if (is_array($language)) {
            $language = $language['language_isocode'];
        }

        // There is no language 'default', check out the correct code
        if ($language === 'default') {
            $language = BackendUtility::getPagesTSconfig(GeneralUtility::_GET('id'))['mod.']['fe_cookies.']['languageManagement.']['defaultLanguageIsocode'] ?? null;
            if (!$language) {
                throw new \RuntimeException('You must set the defaultLanguageIsocode in mod.fe_cookies.languageManagement.defaultLanguageIsocode');
            }
        }

        // Set input type
        $this->tag->addAttribute('type', 'text');

        // Produce field name
        $this->arguments['name'] = 'userdefinedLabels[' . $language . '][' . $label . ']';
        $name = $this->getName();
        $this->registerFieldNameForFormTokenGeneration($name);
        $this->tag->addAttribute('name', $name);

        // Produce field vlaue
        $value = LocalizationUtility::translateUserdefinedLabel($label, $language);
        if ($value !== null) {
            $this->tag->addAttribute('value', $value);
        }

        // Produce a placeholder
        $this->tag->addAttribute(
            'placeholder',
            LocalizationUtility::getLanguageServiceForLanguage($language)->sL('LLL:EXT:fe_cookies/Resources/Private/Language/userdefinedLabels.xlf:' . $label)
        );

        return $this->tag->render();
    }
}
