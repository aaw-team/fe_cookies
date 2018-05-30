<?php
namespace AawTeam\FeCookies\TypoScript\ConditionMatching;

/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use AawTeam\FeCookies\Utility\FeCookiesUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * FeCookies
 */
class FeCookies
{
    /**
     * @return bool
     */
    public static function cookieIsSet()
    {
        return FeCookiesUtility::hasCookie();
    }

    /**
     * @return bool
     */
    public static function cookieIsNotSet()
    {
        return !self::cookieIsSet();
    }

    /**
     * @param string ...$conditionParameters
     * @return bool
     */
    public static function cookieValue(...$conditionParameters)
    {
        if (!FeCookiesUtility::hasCookie()) {
            return false;
        }

        $return = false;
        $cookieValue = FeCookiesUtility::getCookieValue();
        foreach ($conditionParameters as $param) {
            if (!is_string($param)) {
                continue;
            }

            list($operator, $value) = GeneralUtility::trimExplode(' ', $param, true, 2);
            $valuesToMatch = GeneralUtility::trimExplode('|', $value, true);

            switch ($operator) {
                case '=' :
                    if (in_array($cookieValue, $valuesToMatch)) {
                        $return = true;
                    }
                    break 2;
                case '!=' :
                    if (!in_array($cookieValue, $valuesToMatch)) {
                        $return = true;
                    }
                    break 2;
                case '<' :
                    foreach ($valuesToMatch as $valueToMatch) {
                        if ($cookieValue < $valueToMatch) {
                            $return = true;
                            break 3;
                        }
                    }
                    break 2;
                case '>' :
                    foreach ($valuesToMatch as $valueToMatch) {
                        if ($cookieValue > $valueToMatch) {
                            $return = true;
                            break 3;
                        }
                    }
                    break 2;
                case '<=' :
                    foreach ($valuesToMatch as $valueToMatch) {
                        if ($cookieValue <= $valueToMatch) {
                            $return = true;
                            break 3;
                        }
                    }
                    break 2;
                case '>=' :
                    foreach ($valuesToMatch as $valueToMatch) {
                        if ($cookieValue >= $valueToMatch) {
                            $return = true;
                            break 3;
                        }
                    }
                    break 2;
            }
        }

        return $return;
    }

    /**
     * @param string $enableFrontendPlugin
     * @return bool
     */
    public static function showFrontendPlugin($enableFrontendPlugin)
    {
        if (!is_string($enableFrontendPlugin) || !in_array($enableFrontendPlugin, ['0', '1', '-1'])) {
            return false;
        }

        $return = false;
        switch ($enableFrontendPlugin) {
            case '0':
                // Never show
                $return = false;
                break;
            case '1':
                // Show when cookie is not set
                $return = !FeCookiesUtility::hasCookie();
                break;
            case '-1':
                // Show when a backend user is logged in
                $return = isset($GLOBALS['BE_USER']) && is_array($GLOBALS['BE_USER']->user) && $GLOBALS['BE_USER']->user['uid'] > 0;
                break;
        }
        return $return;
    }

    /**
     * @param string $enableFrontendPlugin
     * @return bool
     */
    public static function hideFrontendPlugin($enableFrontendPlugin)
    {
        return !self::showFrontendPlugin($enableFrontendPlugin);
    }
}
