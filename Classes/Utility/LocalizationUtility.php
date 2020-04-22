<?php
namespace AawTeam\FeCookies\Utility;

/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Lang\LanguageService;

/**
 * LocalizationUtility
 */
class LocalizationUtility
{
    /**
     * @var array
     */
    protected static $LOCAL_LANG_userdefined = [];

    /**
     * @var LanguageService[]
     */
    protected static $languageServicePerLanguage = [];

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

    /**
     * @param array $languages
     */
    public static function storeUserdefinedLabels(array $languages)
    {
        $updateLanguageCount = 0;
        foreach ($languages as $language => $labels) {
            if (!self::backendUserHasAccessToLanguage($language)) {
                throw new \RuntimeException('You do not have access to the language "' . htmlspecialchars($language) . '"');
            }
            $xml = self::createXliff($language, $labels);
            if ($xml !== false) {
                self::storeUserdefinedLllFile($language, $xml);
            } else {
                self::removeUserdefinedLllFile($language);
            }
            $updateLanguageCount++;
        }

        // Cleanup
        self::removeEmptyUserdefinedLllFilesDirs();

        return $updateLanguageCount;
    }

    /**
     * @param string $language
     * @throws \InvalidArgumentException
     * @return boolean
     */
    public static function backendUserHasAccessToLanguage($language)
    {
        if (self::isInFrontendMode()) {
            return false;
        }

        if (\TYPO3\CMS\Core\Utility\MathUtility::canBeInterpretedAsInteger($language)) {
            return self::getBackendUserAuthentication()->checkLanguageAccess($language);
        } elseif (!is_string($language) || empty($language)) {
            throw new \InvalidArgumentException('$language must be not empty string or int');
        }
        $defaultLanguageIsocode = BackendUtility::getPagesTSconfig(GeneralUtility::_GET('id'))['mod.']['fe_cookies.']['languageManagement.']['defaultLanguageIsocode'] ?? null;
        if ($language === 'default' || $language === $defaultLanguageIsocode) {
            return self::getBackendUserAuthentication()->checkLanguageAccess(0);
        }

        $rows = SysLanguagesUtility::getSysLanguageRecordByIsocode($language);
        foreach ($rows as $row) {
            if (self::getBackendUserAuthentication()->checkLanguageAccess($row['uid'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param bool $absolute
     * @return string
     */
    public static function getUserdefinedLllFilesDir($absolute = true)
    {
        if (!is_bool($absolute)) {
            throw new \InvalidArgumentException('$absolute must be bool');
        }
        $path = self::getUserdefinedLllFilesBaseDir();

        if (self::isInFrontendMode()) {
            foreach (self::getTypoScriptFrontendController()->rootLine as $page) {
                if (is_dir($path . DIRECTORY_SEPARATOR . 'page_' . $page['uid'])) {
                    $path .= DIRECTORY_SEPARATOR . 'page_' . $page['uid'];
                    break;
                }
            }
        } else {
            $storageMode = BackendUtility::getPagesTSconfig(GeneralUtility::_GET('id'))['mod.']['fe_cookies.']['languageManagement.']['storageMode'] ?? null;
            if (preg_match('~^page:(\\d+)$~', $storageMode, $matches)) {
                $path .= '/page_' . $matches[1];
            } elseif ($storageMode !== 'global') {
                throw new \RuntimeException('Invalid storageMode');
            }
        }

        return $absolute ? $path : substr($path, strlen(PATH_site));
    }

    /**
     * @return string
     */
    public static function getUserdefinedLllFileName($language = 'default')
    {
        if (!is_string($language) || empty($language)) {
            throw new \InvalidArgumentException('$language must be not empty string');
        }
        $fileName = 'userdefined.xlf';
        //if ($language !== 'default') {
        $fileName = $language . '.' . $fileName;
        //}

        return $fileName;
    }

    /**
     * @param string $language
     * @throws \InvalidArgumentException
     * @return bool
     */
    protected static function removeUserdefinedLllFile($language)
    {
        if (!is_string($language) || empty($language)) {
            throw new \InvalidArgumentException('$language must be not empty string');
        }

        $absoluteDirname = self::getUserdefinedLllFilesDir(true);
        $fileName = $absoluteDirname . DIRECTORY_SEPARATOR . self::getUserdefinedLllFileName($language);
        if (is_file($fileName)) {
            return @unlink($fileName);
        }
        return true;
    }

    /**
     * @return string
     */
    protected static function getUserdefinedLllFilesBaseDir()
    {
        return PATH_typo3conf . 'tx_fecookies' . DIRECTORY_SEPARATOR . 'UserdefinedLanguageLabels';
    }

    /**
     *
     */
    protected static function removeEmptyUserdefinedLllFilesDirs()
    {
        if (!self::isInFrontendMode()) {
            $path = self::getUserdefinedLllFilesBaseDir();
            $dirs = GeneralUtility::get_dirs($path);
            if (is_array($dirs)) {
                foreach ($dirs as $dir) {
                    $files = GeneralUtility::getFilesInDir($path . DIRECTORY_SEPARATOR . $dir);
                    if (!is_array($files) || empty($files)) {
                        GeneralUtility::rmdir($path . DIRECTORY_SEPARATOR . $dir, false);
                    }
                }
            }
        }
    }

    /**
     * @param string $language
     * @param string $xml
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return boolean
     */
    protected static function storeUserdefinedLllFile($language, $xml)
    {
        if (!is_string($language) || empty($language)) {
            throw new \InvalidArgumentException('$language must be not empty string');
        } elseif (!is_string($xml) || empty($xml)) {
            throw new \InvalidArgumentException('$xml must be not empty string');
        }

        $absoluteDirname = self::getUserdefinedLllFilesDir(true);
        GeneralUtility::mkdir_deep($absoluteDirname);

        $fileName = self::getUserdefinedLllFileName($language);
        return GeneralUtility::writeFile($absoluteDirname . DIRECTORY_SEPARATOR . $fileName, $xml);
    }

    /**
     * @param string $language
     * @param array $labels
     * @throws \InvalidArgumentException
     * @return string|false
     */
    protected static function createXliff($language, array $labels)
    {
        if (!is_string($language) || empty($language)) {
            throw new \InvalidArgumentException('$language must be not empty string');
        }

        // Filter labels: allowed keys only
        $labels = array_intersect_key($labels, array_flip(self::getAllowedUserdefinedLabels()));
        // Filter labels: not-empty string values only
        $labels = array_filter($labels, function($v) {
            return is_string($v) && trim($v) !== '';
        });

        // No labels left?
        if (empty($labels)) {
            return false;
        }

        $isDefaultLanguage = true;//($language === 'default');
        $domDocument = new \DOMDocument('1.0', 'UTF-8');
        $domDocument->loadXML('<?xml version="1.0" encoding="UTF-8"?>
<xliff version="1.0" xmlns:t3="http://typo3.org/schemas/xliff">
    <file source-language="en" datatype="plaintext" original="messages" date="' . date(\DateTime::ATOM, $GLOBALS['EXEC_TIME']) . '" product-name="fe_cookies">
        <header/>
        <body/>
    </file>
</xliff>', LIBXML_NONET);
        $body = $domDocument->getElementsByTagName('body')->item(0);

        foreach ($labels as $label => $value) {
            $unit = $domDocument->createElement('trans-unit');
            $unit->setAttribute('id', $label);
            // Escape value
            $value = htmlspecialchars($value, ENT_NOQUOTES | ENT_XML1, 'UTF-8');
            if ($isDefaultLanguage) {
                $unit->appendChild($domDocument->createElement('source', $value));
            } else {
                // Try to load default label from the extension-provided files
                $source = self::getLanguageServiceForLanguage($language)->sL('LLL:EXT:fe_cookies/Resources/Private/Language/userdefinedLabels.xlf:' . $label);
                $unit->appendChild($domDocument->createElement('source', $source));
                $unit->appendChild($domDocument->createElement('target', $value));
            }
            $body->appendChild($unit);
        }

        return $domDocument->saveXML();
    }

    /**
     * @param string $language
     * @throws \InvalidArgumentException
     */
    protected static function loadUserdefinedLabels($language, $force = false)
    {
        if (!is_string($language) || empty($language)) {
            throw new \InvalidArgumentException('$language must be not empty string');
        }
        if (isset(self::$LOCAL_LANG_userdefined[$language]) && !$force) {
            return;
        }
        self::$LOCAL_LANG_userdefined[$language] = [];

        $lllFilesDir = self::getUserdefinedLllFilesDir(true);
        if (is_dir($lllFilesDir) && is_readable($lllFilesDir)) {
            $lllFileName = $lllFilesDir . DIRECTORY_SEPARATOR . self::getUserdefinedLllFileName($language);
            if (is_file($lllFileName) && is_readable($lllFileName)) {
                $domDocument = new \DOMDocument('1.0', 'UTF-8');
                if ($domDocument->load($lllFileName, LIBXML_NONET)) {
                    $xpath = new \DOMXPath($domDocument);
                    //$units = $xpath->evaluate('/xliff/file/body/trans-unit/' . ($language === 'default' ? 'source' : 'target'));
                    $units = $xpath->evaluate('/xliff/file/body/trans-unit/source');
                    foreach ($units as $unit) {
                        if ($unit instanceof \DOMElement) {
                            $labelKey = $xpath->evaluate('string(parent::trans-unit/@id)', $unit);
                            if (in_array($labelKey, self::getAllowedUserdefinedLabels())) {
                                self::$LOCAL_LANG_userdefined[$language][$labelKey] = $unit->textContent;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @return string[]
     */
    public static function getAllowedUserdefinedLabels()
    {
        $allLabels = [
            'plugin.label.button.accept',
            'plugin.label.button.close',
            'plugin.label.aria.banner',
        ];

        if (self::isInFrontendMode()) {
            $allowedLabels = $allLabels;
        } else {
            $allowedLabels = GeneralUtility::trimExplode(',', BackendUtility::getPagesTSconfig(GeneralUtility::_GET('id'))['mod.']['fe_cookies.']['languageManagement.']['allowedLanguageLabels'] ?? '', true);
            if (empty($allowedLabels) || (count($allowedLabels) && $allowedLabels[0] === 'all')) {
                $allowedLabels = $allLabels;
            } else {
                $allowedLabels = array_intersect($allowedLabels, $allLabels);
            }
        }
        return $allowedLabels;
    }

    /**
     * @param string $key
     * @param string $language
     * @throws \InvalidArgumentException
     * @return string|null
     */
    public static function translateUserdefinedLabel($key, $language)
    {
        if (!is_string($key) || empty($key)) {
            throw new \InvalidArgumentException('$key must be not empty string');
        } elseif (!is_string($language) || empty($language)) {
            throw new \InvalidArgumentException('$language must be not empty string');
        }
        self::loadUserdefinedLabels($language);
        if (isset(self::$LOCAL_LANG_userdefined[$language][$key])) {
            return self::$LOCAL_LANG_userdefined[$language][$key];
        }
        return null;
    }

    /**
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    public static function getLanguageServiceForLanguage($language = 'default')
    {
        if (!isset(self::$languageServicePerLanguage[$language])) {
            self::$languageServicePerLanguage[$language] = GeneralUtility::makeInstance(LanguageService::class);
            self::$languageServicePerLanguage[$language]->init($language);
        }
        return self::$languageServicePerLanguage[$language];
    }

    /**
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected static function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }

    /**
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected static function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }

    /**
     * @return bool
     */
    protected static function isInFrontendMode()
    {
        return GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Service\EnvironmentService::class)->isEnvironmentInFrontendMode();
    }
}
