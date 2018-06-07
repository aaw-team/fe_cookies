<?php
namespace AawTeam\FeCookies\ViewHelpers\Be;

/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use AawTeam\FeCookies\Utility\SysLanguagesUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * LanguageFlagViewHelper
 */
class LanguageFlagViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
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
        $this->registerArgument('uid', 'int', 'The language uid', false);
        $this->registerArgument('row', 'array', 'The sys_language row', false);
        $this->registerArgument('size', 'string', 'The icon size', false, Icon::SIZE_SMALL);
    }

    /**
     * @throws \InvalidArgumentException
     * @return string
     */
    public function render(): string
    {
        $row = $this->arguments['row'];
        if (!is_array($row)) {
            $uid = $this->arguments['uid'];
            if (!\TYPO3\CMS\Core\Utility\MathUtility::canBeInterpretedAsInteger($uid)) {
                throw new \InvalidArgumentException('Either argument Row" or "uid" must be given');
            }
            if ($uid == -1) {
                $row = [
                    'uid' => -1,
                    'flag' => 'multiple',
                ];
            } elseif ($uid == 0) {
                $row = SysLanguagesUtility::getDefaultLanguage();
            } else {
                $row = BackendUtility::getRecord('sys_language', $uid);
            }
        }

        /** @var IconFactory $iconFactory */
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        return $iconFactory->getIconForRecord('sys_language', $row, $this->arguments['size'])->render();
    }
}
