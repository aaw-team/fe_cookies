<?php
namespace AawTeam\FeCookies\Utility;

/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use AawTeam\FeCookies\Configuration\Configuration;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * FeCookiesUtility
 */
class FeCookiesUtility
{
    const VALUE_DEFAULT = 1;

    /**
     * @return string
     */
    public static function getCookieName()
    {
        return self::getConfiguration()->getName();
    }

    /**
     * @param int $expire
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httpOnly
     * @throws \InvalidArgumentException
     * @return bool
     */
    public static function setCookie()
    {
        $cookieParams = self::getCookieParams();
        return \setcookie(
            $cookieParams['name'],
            $cookieParams['value'],
            $cookieParams['expires'],
            $cookieParams['path'],
            $cookieParams['domain'],
            $cookieParams['secure'],
            $cookieParams['httponly']
        );
    }

    /**
     * @param ResponseInterface $response
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return ResponseInterface
     */
    public static function addCookie(ResponseInterface $response): ResponseInterface
    {
        $cookieParams = self::getCookieParams();
        $cookieHeader = urlencode($cookieParams['name']) . '=' . urlencode($cookieParams['value']);

        if ($cookieParams['expires'] > 0) {
            $cookieHeader .= '; Expires=' . date(\DateTime::COOKIE, $cookieParams['expires']);
        }
        if ($cookieParams['domain']) {
            $cookieHeader .= '; Domain=' . $cookieParams['domain'];
        }
        if ($cookieParams['path']) {
            $cookieHeader .= '; Path=' . $cookieParams['path'];
        }
        if ($cookieParams['secure']) {
            $cookieHeader .= '; Secure';
        }
        if ($cookieParams['httponly']) {
            $cookieHeader .= '; HttpOnly';
        }
        if ($cookieParams['samesite']) {
            $cookieHeader .= '; SameSite=' . $cookieParams['samesite'];
        }

        return $response->withAddedHeader('Set-Cookie', $cookieHeader);
    }

    /**
     * @throws \RuntimeException
     * @return array
     */
    protected static function getCookieParams(): array
    {
        $lifetime = self::getConfiguration()->getLifetime();
        if ($lifetime === null) {
            $expire = 0;
        } elseif (!is_int($lifetime)) {
            throw new \RuntimeException('$expire must be integer or null');
        } else {
            $expire = $GLOBALS['ACCESS_TIME'] + $lifetime;
        }

        $domain = self::getConfiguration()->getDomain();
        if ($domain === null) {
            $domain = '';
        } elseif (!is_string($domain)) {
            throw new \RuntimeException('$domain must be string or null');
        }

        $path = self::getConfiguration()->getPath();
        if ($path === null) {
            $path = $domain ? '/' : GeneralUtility::getIndpEnv('TYPO3_SITE_PATH');
        } elseif (!is_string($path)) {
            throw new \RuntimeException('$path must be string or null');
        }

        $secure = self::getConfiguration()->getSecure();
        if ($secure === null) {
            $secure = (bool)GeneralUtility::getIndpEnv('TYPO3_SSL');
        } elseif (is_bool($secure)) {
            if ($secure && !GeneralUtility::getIndpEnv('TYPO3_SSL')) {
                throw new \RuntimeException('Cannot set secure cookie over an insecure connection');
            }
        } else {
            throw new \RuntimeException('$secure must be bool or null');
        }
        $httpOnly = self::getConfiguration()->getHttpOnly();
        if ($httpOnly !== null && !is_bool($httpOnly)) {
            throw new \RuntimeException('$httpOnly must be bool or null');
        }

        return [
            'name' => self::getCookieName(),
            'value' => self::VALUE_DEFAULT,
            'expires' => $expire,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httponly' => $httpOnly,
            'samesite' => 'Lax',
        ];
    }

    /**
     * @return bool
     */
    public static function hasCookie()
    {
        return isset($_COOKIE[self::getCookieName()]);
    }

    /**
     * @return string|false
     */
    public static function getCookieValue()
    {
        if (self::hasCookie()) {
            return $_COOKIE[self::getCookieName()];
        }
        return false;
    }

    /**
     * @param string $path
     * @param string $domain
     * @throws \InvalidArgumentException
     * @return bool
     */
    public static function removeCookie()
    {
        $domain = self::getConfiguration()->getDomain();
        if ($domain === null) {
            $domain = '';
        } elseif (!is_string($domain)) {
            throw new \InvalidArgumentException('$domain must be string or null');
        }

        $path = self::getConfiguration()->getPath();
        if ($path === null) {
            $path = $domain ? '/' : GeneralUtility::getIndpEnv('TYPO3_SITE_PATH');
        } elseif (!is_string($path)) {
            throw new \InvalidArgumentException('$path must be string or null');
        }

        return \setcookie(self::getCookieName(), null, -1, $path, $domain);
    }

    /**
     * @return Configuration
     */
    protected static function getConfiguration()
    {
        return GeneralUtility::makeInstance(Configuration::class);
    }
}
