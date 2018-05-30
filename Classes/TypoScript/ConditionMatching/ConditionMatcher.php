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
 * ConditionMatcher
 */
class ConditionMatcher
{
    /**
     * @param array $conditionParameters
     * @return bool|null
     */
    public static function evaluateCondition(array $conditionParameters)
    {
        // Stay quiet when no arguments are given
        if (empty($conditionParameters)) {
            return null;
        }

        // Remove leading '='
        if (substr($conditionParameters[0], 0, 1) === '=') {
            $conditionParameters[0] = trim(substr($conditionParameters[0], 1));
        }

        $result = null;
        foreach ($conditionParameters as $conditionParameter) {
            if (preg_match('~^(cookieSet|cookieValue|showFrontendPlugin)\s*([!<>=]+)\s*(.+)$~', $conditionParameter, $matches)) {
                $rule = $matches[1];
                $operator = $matches[2];
                $value = trim($matches[3]);
                switch ($rule) {
                    case 'cookieSet' :
                        $result = self::cookieSet($operator, $value);
                        break;
                    case 'cookieValue' :
                        $result = self::cookieValue($operator, $value);
                        break;
                    case 'showFrontendPlugin' :
                        $result = self::showFrontendPlugin($operator, $value);
                        break;
                }
                if ($result !== null) {
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * @param string $operator
     * @param string $value
     * @return bool|null
     */
    protected static function showFrontendPlugin($operator, $value)
    {
        if (!in_array($operator, ['=', '!'])) {
            return null;
        }
        $hasCookie = FeCookiesUtility::hasCookie();
        $return = null;
        switch ($value) {
            case '0':
                // Never show
                $return = false;
                break;
            case '1':
                // Show when cookie is not set
                $return = !$hasCookie;
                break;
            case '-1':
                // Show when a backend user is logged in
                $return = isset($GLOBALS['BE_USER']) && is_array($GLOBALS['BE_USER']->user) && $GLOBALS['BE_USER']->user['uid'] > 0;
                break;
        }
        if ($operator === '!' && is_bool($return)) {
            $return = !$return;
        }
        return $return;
    }

    /**
     * @param string $operator
     * @param string $value
     * @return bool|null
     */
    protected static function cookieSet($operator, $value)
    {
        $return = null;
        if ($operator === '=') {
            $isCookieSet = FeCookiesUtility::hasCookie() ? '1' : '0';
            if ($value === $isCookieSet) {
                $return = true;
            }
        }
        return $return;
    }

    /**
     * @param string $operator
     * @param string $value
     * @return bool|null
     */
    protected static function cookieValue($operator, $value)
    {
        if (!FeCookiesUtility::hasCookie()) {
            return null;
        }
        $return = null;
        $cookieValue = FeCookiesUtility::getCookieValue();
        $valuesToMatch = GeneralUtility::trimExplode('|', $value, true);
        switch ($operator) {
            case '=' :
                if (in_array($cookieValue, $valuesToMatch)) {
                    $return = true;
                }
                break;
            case '!=' :
                if (!in_array($cookieValue, $valuesToMatch)) {
                    $return = true;
                }
                break;
            case '<' :
                foreach ($valuesToMatch as $valueToMatch) {
                    if ($cookieValue < $valueToMatch) {
                        $return = true;
                        break 2;
                    }
                }
                break;
            case '>' :
                foreach ($valuesToMatch as $valueToMatch) {
                    if ($cookieValue > $valueToMatch) {
                        $return = true;
                        break 2;
                    }
                }
                break;
            case '<=' :
                foreach ($valuesToMatch as $valueToMatch) {
                    if ($cookieValue <= $valueToMatch) {
                        $return = true;
                        break 2;
                    }
                }
                break;
            case '>=' :
                foreach ($valuesToMatch as $valueToMatch) {
                    if ($cookieValue >= $valueToMatch) {
                        $return = true;
                        break 2;
                    }
                }
                break;
        }

        return $return;
    }
}
