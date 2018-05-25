<?php
namespace AawTeam\FeCookies\ViewHelpers\Form;

/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\FormProtection\FormProtectionFactory;

/**
 * FormProtectionViewHelper
 */
class FormProtectionViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Form\AbstractFormFieldViewHelper
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
        // Make argument 'name' mandatory
        $this->overrideArgument('name', 'string', 'Name of input tag', true);
        // Register additional arguments
        $this->registerArgument('action', 'string', 'The "action" argument for generateToken()', false, '');
        $this->registerArgument('formInstanceName', 'string', 'The "formInstanceName" argument for generateToken()', false, '');
    }

    /**
     * @throws \InvalidArgumentException
     * @return string
     */
    public function render()
    {
        $name = $this->prefixFieldName($this->arguments['name']);
        $this->registerFieldNameForFormTokenGeneration($name);

        $this->tag->addAttribute('type', 'hidden');
        $this->tag->addAttribute('name', $name);
        $this->tag->addAttribute('value', FormProtectionFactory::get()->generateToken('fe_cookies_formprotection', (string)$this->arguments['action'], (string)$this->arguments['formInstanceName']));
        FormProtectionFactory::get()->persistSessionToken();
        return $this->tag->render();
    }
}
