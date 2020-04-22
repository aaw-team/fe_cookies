<?php
declare(strict_types=1);
namespace AawTeam\FeCookies\ExpressionLanguage;
/*
 * Copyright 2020 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use AawTeam\FeCookies\TypoScript\ConditionMatching\FeCookies;
use AawTeam\FeCookies\Utility\FeCookiesUtility;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * TypoScriptFunctionProvider
 */
class TypoScriptFunctionProvider implements ExpressionFunctionProviderInterface
{
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface::getFunctions()
     */
    public function getFunctions()
    {
        return [
            $this->getCookieIsSetFunction(),
            $this->getCookieIsNotSetFunction(),
            $this->getCookieValueFunction(),
            $this->getShowFrontendPluginFunction(),
            $this->getHideFrontendPluginFunction(),
        ];
    }

    /**
     * @return ExpressionFunction
     */
    protected function getCookieIsSetFunction(): ExpressionFunction
    {
        return new ExpressionFunction('cookieIsSet', function () {
            // Not implemented, we only use the evaluator
        }, function ($arguments) {
            return FeCookies::cookieIsSet();
        });
    }

    /**
     * @return ExpressionFunction
     */
    protected function getCookieIsNotSetFunction(): ExpressionFunction
    {
        return new ExpressionFunction('cookieIsNotSet', function () {
            // Not implemented, we only use the evaluator
        }, function ($arguments) {
            return FeCookies::cookieIsNotSet();
        });
    }

    /**
     * @return ExpressionFunction
     */
    protected function getCookieValueFunction(): ExpressionFunction
    {
        return new ExpressionFunction('cookieValue', function () {
            // Not implemented, we only use the evaluator
        }, function ($arguments) {
            return FeCookiesUtility::getCookieValue();
        });
    }

    /**
     * @return ExpressionFunction
     */
    protected function getShowFrontendPluginFunction(): ExpressionFunction
    {
        return new ExpressionFunction('showFrontendPlugin', function ($enableFrontendPlugin) {
            // Not implemented, we only use the evaluator
        }, function ($arguments, $enableFrontendPlugin) {
            return FeCookies::showFrontendPlugin((string)$enableFrontendPlugin);
        });
    }

    /**
     * @return ExpressionFunction
     */
    protected function getHideFrontendPluginFunction(): ExpressionFunction
    {
        return new ExpressionFunction('hideFrontendPlugin', function ($enableFrontendPlugin) {
            // Not implemented, we only use the evaluator
        }, function ($arguments, $enableFrontendPlugin) {
            return FeCookies::hideFrontendPlugin((string)$enableFrontendPlugin);
        });
    }
}
