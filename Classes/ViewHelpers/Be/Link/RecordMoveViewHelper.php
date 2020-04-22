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
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * RecordMoveViewHelper
 */
class RecordMoveViewHelper extends AbstractTagBasedViewHelper
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
        $this->registerArgument('object', AbstractDomainObject::class, 'Model to be moved', false);
        $this->registerArgument('table', 'string', 'The table', false);
        $this->registerArgument('uid', 'int', 'The UID of the record to be edited', false);
        $this->registerArgument('objects', \ArrayAccess::class, 'All objects that are used in the f:for', true);
        $this->registerArgument('iterator', 'array', 'The itertator (from a f:for view helper)', true);
        $this->registerArgument('direction', 'string', 'The direction: "up" or "down"', false, 'up');
    }

    /**
     * @return mixed|string
     */
    public function render()
    {
        $direction = strtolower($this->arguments['direction']);
        $iterator = $this->arguments['iterator'];
        if ($this->arguments['object'] instanceof AbstractDomainObject) {
            $table = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper::class)->convertClassNameToTableName(get_class($this->arguments['object']));
            $uid = $this->arguments['object']->getUid();
            $proceed = $this->arguments['object']->_getProperty('_localizedUid') === $uid;
        } else {
            $table = $this->arguments['table'];
            $uid = $this->arguments['uid'];
            $row = BackendUtility::getRecord($table, $uid);
            $proceed = $row[$GLOBALS['TCA'][$table]['ctrl']['transOrigPointerField']] == 0;
        }

        if (!$proceed) {
            return '';
        }

        /** @var \ArrayAccess $objects */
        $objects = $this->arguments['objects'];

        $position = 0;
        if ($direction == 'up') {
            if ($iterator['isFirst']) {
                return '';
            } elseif ($iterator['index'] == 1) {
                $position = 1;
            } elseif (isset($objects[$iterator['index'] - 2])) {
                $position = ($objects[$iterator['index'] - 2] instanceof AbstractDomainObject)
                    ? $objects[$iterator['index'] - 2]->getUid()
                    : $objects[$iterator['index'] - 2]['uid'];
                $position *= -1;
            } else {
                // something went wrong?
            }
        } elseif ($direction == 'down') {
            if ($iterator['isLast']) {
                return '';
            } elseif (isset($objects[$iterator['index'] + 1])) {
                $position = ($objects[$iterator['index'] + 1] instanceof AbstractDomainObject)
                    ? $objects[$iterator['index'] + 1]->getUid()
                    : $objects[$iterator['index'] + 1]['uid'];
                $position *= -1;
            } else {
                // something went wrong?
            }
        } else {
            throw new \InvalidArgumentException('Invalid argument "direction": "' . $direction . '"');
        }

        if ($position === 0) {
            return '';
        }

        $parameters = [
            'cmd' => [
                $table => [
                    $uid => [
                        'move' => $position,
                    ],
                ],
            ],
            'redirect' => $this->arguments['returnUrl'] ?? GeneralUtility::getIndpEnv('REQUEST_URI'),
        ];

        $uri = BackendUtility::getModuleUrl('tce_db', $parameters);

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
