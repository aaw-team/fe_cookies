<?php
declare(strict_types=1);
namespace AawTeam\FeCookies\ExpressionLanguage;
/*
 * Copyright 2020 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\ExpressionLanguage\AbstractProvider;

/**
 * ExpressionLanguageProvider
 */
class ExpressionLanguageProvider extends AbstractProvider
{
    /**
     *
     */
    public function __construct()
    {
        $this->expressionLanguageProviders = [
            TypoScriptFunctionProvider::class
        ];
    }
}
