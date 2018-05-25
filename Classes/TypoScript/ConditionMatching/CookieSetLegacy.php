<?php
namespace AawTeam\FeCookies\TypoScript\ConditionMatching;

/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use AawTeam\FeCookies\Utility\FeCookiesUtility;

/**
 * CookieSetLegacy
 */
class CookieSetLegacy
{
    /**
     * @param array $conditionParameters
     */
    public static function matchCondition()
    {
        return FeCookiesUtility::hasCookie();
    }
}
