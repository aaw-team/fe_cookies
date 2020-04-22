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
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * RecordChangeViewHelper
 */
class RecordChangeViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'a';

    /**
     * {@inheritDoc}
     * @see AbstractTagBasedViewHelper::initializeArguments()
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerArgument('returnUrl', 'string', 'The return URL', false, null);
        $this->registerArgument('table', 'string', 'The table', true);
        $this->registerArgument('uid', 'int', 'The UID of the record to be edited', true);
        $this->registerArgument('cmd', 'string', 'The command: "hide", "unhide", "delete"', true);
    }

    /**
     * @return mixed|string
     */
    public function render()
    {
        $cmd = strtolower($this->arguments['cmd']);
        $table = $this->arguments['table'];
        $uid = $this->arguments['uid'];
        $parameters = [];

        switch ($cmd) {
            case 'delete':
                $parameters['cmd'] = [
                    $table => [
                        $uid => [
                            'delete' => '1'
                        ]
                    ]
                ];
                $moduleName = 'tce_db';
                break;
            case 'hide':
            case 'unhide':
                $hiddenField = $GLOBALS['TCA'][$table]['ctrl']['enablecolumns']['disabled'];
                $parameters['data'] = [
                    $table => [
                        $uid => [
                            $hiddenField => ($cmd == 'hide')
                        ]
                    ]
                ];
                $moduleName = 'tce_db';
                break;
        }

        $parameters['redirect'] = $this->arguments['redirect'] ?? GeneralUtility::getIndpEnv('REQUEST_URI');

        $uri = BackendUtility::getModuleUrl($moduleName, $parameters);

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
