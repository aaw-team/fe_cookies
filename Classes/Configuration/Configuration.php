<?php
namespace AawTeam\FeCookies\Configuration;

/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\PageGenerator;

/**
 * Configuration
 */
class Configuration implements SingletonInterface
{
    const CACHE_IDENTIFIER = 'fecookies_configuration';
    const ENTRY_IDENTIFIER = 'configuration';
    const GLOBAL_KEY = '_global_';
    const DEFAULT_COOKIE_NAME = 'tx_fecookies';

    // Option keys
    const OPTION_NAME = 'name';
    const OPTION_LIFETIME = 'lifetime';
    const OPTION_DOMAIN = 'domain';
    const OPTION_PATH = 'path';
    const OPTION_SECURE = 'secure';
    const OPTION_HTTPONLY = 'httpOnly';

    /**
     * @var array
     */
    protected $configuration = [];

    /**
     *
     */
    public function __construct()
    {
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cache */
        $cache = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Cache\CacheManager::class)->getCache(self::CACHE_IDENTIFIER);
        if ($cache->has(self::ENTRY_IDENTIFIER)) {
            $configuration = $cache->requireOnce(self::ENTRY_IDENTIFIER);
        } else {
            $configuration = $this->build();
            $cache->set(self::ENTRY_IDENTIFIER, 'return ' . ArrayUtility::arrayExport($configuration) . ';');
        }
        $configuration = $this->extractCurrentDomainConfiguration($configuration);
        $this->checkConfiguration($configuration);
        $this->configuration = $configuration;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->configuration[self::OPTION_NAME];
    }

    /**
     * @return string|null
     */
    public function getDomain()
    {
        return $this->configuration[self::OPTION_DOMAIN];
    }

    /**
     * @return string|null
     */
    public function getPath()
    {
        return $this->configuration[self::OPTION_PATH];
    }

    /**
     * @return int|null
     */
    public function getLifetime()
    {
        return $this->configuration[self::OPTION_LIFETIME];
    }

    /**
     * @return bool|null
     */
    public function getSecure()
    {
        return $this->configuration[self::OPTION_SECURE];
    }

    /**
     * @return bool|null
     */
    public function getHttpOnly()
    {
        return $this->configuration[self::OPTION_HTTPONLY];
    }

    /**
     * @return array
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @throws \RuntimeException
     * @return string
     */
    public function getJsConfigurationFile()
    {
        $currentConfiguration = $this->configuration;
        unset($currentConfiguration[self::OPTION_HTTPONLY]);
        $js = '// Auto-generated JavaScript by ' . __METHOD__ . '
window.AawTeam=window.AawTeam||{};
window.AawTeam.fe_cookies_configuration=' . json_encode($currentConfiguration) . ';
';
        $filename = PageGenerator::inline2TempFile($js, 'js');
        if (!file_exists(GeneralUtility::getFileAbsFileName($filename))) {
            throw new \RuntimeException('Cannot generate JS configuration file');
        }
        return $filename;
    }

    /**
     * @param array $configuration
     * @throws \RuntimeException
     */
    protected function checkConfiguration(array $configuration)
    {
        if (!array_key_exists(self::OPTION_NAME, $configuration)
            || !is_string($configuration[self::OPTION_NAME])
            // Cookie name is a <token>, @see section "2.2 Basic Rules" in RFC 2616 (https://www.ietf.org/rfc/rfc2616.txt)
            || preg_match('/[^\x21\x23-\x27\x2a\x2b\x2d\x2e\x30-\x39\x41-\x5a\x5e-\x7a\x7c\x7e]/', $configuration[self::OPTION_NAME], $matches)
        ) {
            throw new \RuntimeException('The name of a cookie must be a not empty string, containing US-ASCII, except ASCII Control characters (0-31;127), space, tab or one of the following characters: ()<>@,;:"/[]?={}');
        }
        if (!array_key_exists(self::OPTION_DOMAIN, $configuration)
            || ($configuration[self::OPTION_DOMAIN] !== null && !is_string($configuration[self::OPTION_DOMAIN]))
        ) {
            throw new \RuntimeException('The domain of a cookie must be string or null');
        }

        if (!array_key_exists(self::OPTION_PATH, $configuration)
            || ($configuration[self::OPTION_PATH] !== null && !is_string($configuration[self::OPTION_PATH]))
        ) {
            throw new \RuntimeException('The path of a cookie must be string or null');
        }

        if (!array_key_exists(self::OPTION_LIFETIME, $configuration)
            || ($configuration[self::OPTION_LIFETIME] !== null && (!is_int($configuration[self::OPTION_LIFETIME]) || $configuration[self::OPTION_LIFETIME] < 0))
        ) {
            throw new \RuntimeException('The lifetime of a cookie must be positive integer, zero or null');
        }

        if (!array_key_exists(self::OPTION_SECURE, $configuration)
            || ($configuration[self::OPTION_SECURE] !== null && !is_bool($configuration[self::OPTION_SECURE]))
        ) {
            throw new \RuntimeException('The secure-parameter of a cookie must be bool or null');
        }

        if (!array_key_exists(self::OPTION_HTTPONLY, $configuration)
            || ($configuration[self::OPTION_HTTPONLY] !== null && !is_bool($configuration[self::OPTION_HTTPONLY]))
        ) {
            throw new \RuntimeException('The httpOnly-parameter of a cookie must be bool or null');
        }
    }

    /**
     * @param array $configuration
     * @return array
     */
    protected function extractCurrentDomainConfiguration(array $configuration)
    {
        $currentDomain = GeneralUtility::getIndpEnv('TYPO3_HOST_ONLY');
        if (array_key_exists($currentDomain, $configuration) && is_array($configuration[$currentDomain])) {
            $domainConfiguration = $configuration[$currentDomain];
        } else {
            $domainConfiguration = $configuration[self::GLOBAL_KEY];
        }
        return $domainConfiguration;
    }

    /**
     * @return array
     */
    protected function build()
    {
        $configuration = $this->getDefaultConfiguration();
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['fe_cookies'])) {
            ArrayUtility::mergeRecursiveWithOverrule($configuration, $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['fe_cookies']);
        }
        foreach ($configuration as $domain => $config) {
            if ($domain === self::GLOBAL_KEY) {
                continue;
            }
            $domainConfig = $configuration[self::GLOBAL_KEY];
            ArrayUtility::mergeRecursiveWithOverrule($domainConfig, $config);
            $configuration[$domain] = $domainConfig;
        }
        return $configuration;
    }

    /**
     * @return array
     */
    protected function getDefaultConfiguration()
    {
        return [
            self::GLOBAL_KEY => [
                self::OPTION_NAME => self::DEFAULT_COOKIE_NAME,
                self::OPTION_LIFETIME => null,
                self::OPTION_DOMAIN => null,
                self::OPTION_PATH => null,
                self::OPTION_SECURE => null,
                self::OPTION_HTTPONLY => null,
            ],
        ];
    }
}
