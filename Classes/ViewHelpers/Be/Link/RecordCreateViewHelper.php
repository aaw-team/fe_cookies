<?php
namespace AawTeam\FeCookies\ViewHelpers\Be\Link;

/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * RecordCreateViewHelper
 */
class RecordCreateViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'a';

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     * @inject
     */
    protected $configurationManager;

    /**
     * {@inheritDoc}
     * @see \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper::initializeArguments()
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerArgument('returnUrl', 'string', 'The return URL', false, null);
        $this->registerArgument('table', 'string', 'The table', true);
    }

    /**
     * @return mixed|string
     */
    public function render()
    {
        $pid = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK)['persistence']['storagePid'];
        $parameters = [
            'edit' => [
                $this->arguments['table'] => [
                    $pid => 'new',
                ],
            ],
            'returnUrl' => $this->arguments['returnUrl'] ?? GeneralUtility::getIndpEnv('REQUEST_URI'),
        ];

        $uri = BackendUtility::getModuleUrl('record_edit', $parameters);

        if ($uri !== '') {
            $this->tag->addAttribute('href', $uri);
            $this->tag->setContent($this->renderChildren());
            $result = $this->tag->render();
        } else {
            $result = $this->renderChildren();
        }
        return $result;
    }
}
