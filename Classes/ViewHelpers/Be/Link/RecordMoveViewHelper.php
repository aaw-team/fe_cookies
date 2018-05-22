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
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;

/**
 * RecordMoveViewHelper
 */
class RecordMoveViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'a';

    /**
     * {@inheritDoc}
     * @see \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper::initializeArguments()
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerArgument('returnUrl', 'string', 'The return URL', false, null);
        $this->registerArgument('object', AbstractDomainObject::class, 'Model to be moved', true);
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
        $object = $this->arguments['object'];
        /** @var \ArrayAccess $objects */
        $objects = $this->arguments['objects'];

        $position = 0;
        if ($direction == 'up') {
            if ($iterator['isFirst']) {
                return '';
            } elseif ($iterator['index'] == 1) {
                $position = 1;
            } elseif ($objects->offsetExists($iterator['index'] - 2)) {
                $position = $objects->offsetGet($iterator['index'] - 2)->getUid() * -1;
            } else {
                // something went wrong?
            }
        } elseif ($direction == 'down') {
            if ($iterator['isLast']) {
                return '';
            } elseif ($objects->offsetExists($iterator['index'] + 1)) {
                $position = $objects->offsetGet($iterator['index'] + 1)->getUid() * -1;
            } else {
                // something went wrong?
            }
        } else {
            throw new \InvalidArgumentException('Invalid argument "direction": "' . $direction . '"');
        }

        if ($position === 0) {
            return '';
        }

        /** @var DataMapper $dataMapper */
        $dataMapper = $this->objectManager->get(DataMapper::class);
        $parameters = [
            'cmd' => [
                $dataMapper->convertClassNameToTableName(get_class($object)) => [
                    $object->getUid() => [
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
