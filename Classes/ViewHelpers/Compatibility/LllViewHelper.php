<?php
namespace AawTeam\FeCookies\ViewHelpers\Compatibility;

/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * RecordIconViewHelper
 */
class LllViewHelper extends AbstractViewHelper
{
    /**
     * @var bool
     */
    protected static $isTYPO3V8OrGreater = null;

    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * {@inheritDoc}
     * @see AbstractViewHelper::initializeArguments()
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('key', 'string', 'The locallang label key', true);
        $this->registerArgument('file', 'string', 'The locallang file name', true);
        $this->registerArgument('ext', 'string', 'The sysext key', false, 'lang');
    }

    /**
     * @throws \InvalidArgumentException
     * @return string
     */
    public function render(): string
    {
        $output = '';
        if ($this->arguments['ext'] === 'lang') {
            if ($this->isV8OrGreater()) {
                $output = 'LLL:EXT:lang/Resources/Private/Language/' . $this->arguments['file'] . ':' . $this->arguments['key'];
            }
            else {
                $output = 'LLL:EXT:lang/' . $this->arguments['file'] . ':' . $this->arguments['key'];
            }
        } else {
            throw new \InvalidArgumentException('Currently supported sysexts: "lang"');
        }
        return $output;
    }

    /**
     * @return bool
     */
    protected function isV8OrGreater()
    {
        if (self::$isTYPO3V8OrGreater === null) {
            self::$isTYPO3V8OrGreater = version_compare(TYPO3_version, '8', '>=');
        }
        return self::$isTYPO3V8OrGreater;
    }
}
