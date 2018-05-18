<?php
namespace AawTeam\FeCookies\ViewHelpers\Be;

/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;

/**
 * RecordIconViewHelper
 */
class RecordIconViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * {@inheritDoc}
     * @see \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper::initializeArguments()
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('table', 'string', 'The table name', false, '');
        $this->registerArgument('row', 'array', 'The data row', false, []);
        $this->registerArgument('object', \TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject::class, 'The model instance');
        $this->registerArgument('size', 'string', 'The icon size', false, Icon::SIZE_SMALL);
    }

    /**
     * @throws \InvalidArgumentException
     * @return string
     */
    public function render(): string
    {
        if ($this->arguments['object']) {
            if (!is_object($this->arguments['object']) || !($this->arguments['object'] instanceof \TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject)) {
                throw new \InvalidArgumentException('Argument "object" must be ' . \TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject::class);
            }
            $modelClassName = get_class($this->arguments['object']);
            $tableName = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper::class)->convertClassNameToTableName($modelClassName);
            $row = BackendUtility::getRecord($tableName, $this->arguments['object']->getUid());
        } else {
            $tableName = $this->arguments['table'];
            $row = $this->arguments['row'];
        }

        /** @var IconFactory $iconFactory */
        $iconFactory = $this->objectManager->get(IconFactory::class);
        return $iconFactory->getIconForRecord($tableName, $row, $this->arguments['size'])->render();
    }
}
