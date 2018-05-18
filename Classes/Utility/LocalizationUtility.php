<?php
namespace AawTeam\FeCookies\Utility;

/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * LocalizationUtility
 */
class LocalizationUtility
{
    /**
     * @param string $key
     * @param array $arguments
     * @throws \InvalidArgumentException
     * @return string
     */
    public static function translate($key, $arguments = [])
    {
        if (empty($arguments)) {
            $arguments = null;
        }
        $translated = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($key, 'fe_cookies', $arguments);
        if ($translated === null) {
            // No translation found, return $key
            return $key;
        } elseif ($translated === false) {
            // (most likely) vsprintf returned false because of an invalid argument count error
            throw new \InvalidArgumentException('Invalid argument count in "' . htmlspecialchars($key) . '"', 1526638263);
        }
        return $translated;
    }
}
