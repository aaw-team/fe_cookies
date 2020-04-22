<?php
namespace AawTeam\FeCookies\ViewHelpers\Be\Link;

/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * RecordEditViewHelper
 */
class RecordEditViewHelper extends AbstractTagBasedViewHelper
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
        $this->registerArgument('fields', 'array', 'The fields to be edited (default: all)', false, null);
    }

    /**
     * @return mixed|string
     */
    public function render()
    {
        $parameters = [
            'edit' => [
                $this->arguments['table'] => [
                    $this->arguments['uid'] => 'edit',
                ],
            ],
            'returnUrl' => $this->arguments['returnUrl'] ?? GeneralUtility::getIndpEnv('REQUEST_URI'),
        ];
        if (is_array($this->arguments['fields'])) {
            $parameters['columnsOnly'] = implode(',', $this->arguments['fields']);
        }
        $uri = GeneralUtility::makeInstance(UriBuilder::class)->buildUriFromRoute('record_edit', $parameters);

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
