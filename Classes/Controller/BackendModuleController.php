<?php
namespace AawTeam\FeCookies\Controller;

/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use AawTeam\FeCookies\Utility\LocalizationUtility;
use AawTeam\FeCookies\Utility\SysLanguagesUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\FormProtection\FormProtectionFactory;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\TypoScript\ExtendedTemplateService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * BackendModuleController
 */
class BackendModuleController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * @var \AawTeam\FeCookies\Domain\Repository\BlockRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $blockRepository;

    /**
     * @var \TYPO3\CMS\Core\TypoScript\ExtendedTemplateService
     */
    protected $templateService;

    /**
     * @var array
     */
    protected $allConstants = [];

    /**
     * @var array
     */
    protected $constants = [];

    /**
     * @var array
     */
    protected $templateRow = [];

    /**
     *
     */
    public function indexAction()
    {
        if (!$this->isRequestForPage()) {
            $this->forward('infoBox', null, null, [
                'message' => 'sysmessage.nopage.text',
                'title' => 'sysmessage.nopage.heading',
                // @todo replace AbstractMessage::INFO with \TYPO3\CMS\Fluid\ViewHelpers\Be\InfoboxViewHelper::STATE_INFO when dropping support for TYPO3 < 7.2
                'state' => \TYPO3\CMS\Core\Messaging\AbstractMessage::INFO,
            ]);
        }

        $pageUid = (int)GeneralUtility::_GP('id');

        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface $querySettings */
        $querySettings = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface::class);
        $querySettings->setStoragePageIds([$pageUid]);
        $querySettings->setIgnoreEnableFields(true);
        $this->blockRepository->setDefaultQuerySettings($querySettings);

        // Do not display records in languages, the user has no access to
        $blocks = $this->blockRepository->findAllForBackendList();
        foreach (array_keys($blocks) as $key) {
            if (!$this->getBackendUserAuthentication()->checkLanguageAccess($blocks[$key]['sys_language_uid'])) {
                unset($blocks[$key]);
            }
        }

        $this->view->assignMultiple([
            'pageUid' => $pageUid,
            'blocks' => $blocks,
            'showSettings' => $this->userHasAccessToSettings(),
            'showLanguage' => $this->userHasAccessToLanguage(),
            'toolbarPartial' => 'Index',
        ]);
    }

    /**
     * @return bool
     */
    protected function userHasAccessToSettings()
    {
        return $this->getBackendUserAuthentication()->isAdmin() || (BackendUtility::getPagesTSconfig($pageUid)['mod.']['fe_cookies.']['settingsManagement.']['enable'] ?? false);
    }

    /**
     * @return bool
     */
    protected function userHasAccessToLanguage()
    {
        return $this->getBackendUserAuthentication()->isAdmin() || (BackendUtility::getPagesTSconfig($pageUid)['mod.']['fe_cookies.']['languageManagement.']['enable'] ?? false);
    }

    /**
     * @param array userdefinedLabels
     * @param string $csrfToken
     */
    public function languageAction(array $userdefinedLabels = null, $csrfToken = null)
    {
        // Check access to this function
        if (!$this->userHasAccessToLanguage()) {
            $this->addFlashMessage('sysmessage.noaccesstolanguage.text', 'sysmessage.noaccess.heading', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
            $this->redirect('index');
        }
        if (!$this->isRequestForPage()) {
            $this->forward('infoBox', null, null, [
                'message' => 'sysmessage.nopage.text',
                'title' => 'sysmessage.nopage.heading',
                'state' => \TYPO3\CMS\Core\Messaging\AbstractMessage::INFO,
            ]);
        }
        $pageUid = (int)GeneralUtility::_GP('id');

        $allowedLabels = LocalizationUtility::getAllowedUserdefinedLabels();

        if ($this->request->getMethod() === 'POST') {
            // Check the csrf token
            if ($csrfToken === null || !FormProtectionFactory::get()->validateToken($csrfToken, 'fe_cookies_formprotection')) {
                $this->response->setContent('Security alert: CSRF token validation failed');
                throw new \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException();
            }

            $count = LocalizationUtility::storeUserdefinedLabels($userdefinedLabels);
            $this->addFlashMessage('sysmessage.success.languagelabelsstore');

            // Clear caches
            /** @var CacheManager $cacheManager */
            $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
            $cacheManager->getCache('l10n')->flush();
            $cacheManager->flushCachesInGroup('pages');
            return $this->redirect('language');
        }

        $languages = SysLanguagesUtility::getSysLanguageRecords($pageUid, true);
        // Remove the languages which the user does not have access to
        foreach (array_keys($languages) as $languageUid) {
            if (!LocalizationUtility::backendUserHasAccessToLanguage($languageUid)) {
                unset($languages[$languageUid]);
            }
        }

        $this->view->assignMultiple([
            'pageUid' => $pageUid,
            'toolbarPartial' => 'Language',
            'languages' => $languages,
            'allowedLabels' => $allowedLabels,
        ]);
    }

    /**
     * This method does quite the same as
     * \TYPO3\CMS\Tstemplate\Controller\TypoScriptTemplateConstantEditorModuleFunctionController
     *
     * @see \TYPO3\CMS\Tstemplate\Controller\TypoScriptTemplateConstantEditorModuleFunctionController
     * @param string $csrfToken
     */
    public function settingsAction($csrfToken = null)
    {
        // Check access to this function
        if (!$this->userHasAccessToSettings()) {
            $this->addFlashMessage('sysmessage.noaccesstosettings.text', 'sysmessage.noaccess.heading', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
            $this->redirect('index');
        }
        if (!$this->isRequestForPage()) {
            $this->forward('infoBox', null, null, [
                'message' => 'sysmessage.nopage.text',
                'title' => 'sysmessage.nopage.heading',
            ]);
        }

        $pageUid = (int)GeneralUtility::_GP('id');

        /** @var PageRenderer $pagerenderer */
        $pagerenderer = $this->objectManager->get(PageRenderer::class);
        $pagerenderer->loadRequireJsModule('TYPO3/CMS/Tstemplate/ConstantEditor');

        // Initialize TemplateService
        $templateUid = (int)BackendUtility::getPagesTSconfig($pageUid)['mod.']['fe_cookies.']['settingsManagement.']['templateUid'] ?? 0;
        $templatePid = (int)BackendUtility::getPagesTSconfig($pageUid)['mod.']['fe_cookies.']['settingsManagement.']['templatePid'] ?? 0;
        if (!$templatePid || $templatePid < 1) {
            $templatePid = $pageUid;
        }

        $templateExists = $this->initializeTemplateService($templatePid, $templateUid);
        if ($templateExists) {
            // Store values, if needed
            if ($this->request->getMethod() === 'POST') {
                // Check the csrf token
                if ($csrfToken === null || !FormProtectionFactory::get()->validateToken($csrfToken, 'fe_cookies_formprotection')) {
                    $this->response->setContent('Security alert: CSRF token validation failed');
                    throw new \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException();
                }

                // Check wether the formdata contains allowed properties
                $postData = GeneralUtility::_POST();
                if ($this->checkConstantsFromRequest($postData)) {
                    $this->templateService->changed = 0;
                    $this->templateService->ext_procesInput($postData, [], $this->allConstants, $this->templateRow);
                    if ($this->templateService->changed) {
                        $constantsString = implode(LF, $this->templateService->raw);
                        if ($this->storeTemplateRecord($constantsString)) {
                            // Clear cache
                            /** @var DataHandler $dataHandler */
                            $dataHandler = GeneralUtility::makeInstance(DataHandler::class);

                            // If the user is not allowed to clear 'all' caches, setup an alternative user object
                            // just for this purpose
                            $extendUserPermissionsForCacheClearing = !($this->getBackendUserAuthentication()->getTSConfig()['options.']['clearCache.']['all'] ?? false || ($this->getBackendUserAuthentication()->isAdmin() && $this->getBackendUserAuthentication()->getTSConfig()['options.']['clearCache.']['all'] !== '0'));
                            if ($extendUserPermissionsForCacheClearing) {
                                $beUser = clone ($this->getBackendUserAuthentication());
                                is_array($beUser->userTS['options.']) || $beUser->userTS['options.'] = [];
                                is_array($beUser->userTS['options.']['clearCache.']) || $beUser->userTS['options.']['clearCache.'] = [];
                                $beUser->userTS['options.']['clearCache.']['all'] = '1';
                                $dataHandler->start([], [], $beUser);
                            } else {
                                $dataHandler->start([], []);
                            }
                            $dataHandler->clear_cacheCmd('all');

                            // Remove the objects from memory
                            unset($dataHandler);
                            if ($extendUserPermissionsForCacheClearing) {
                                unset($beUser);
                            }

                            $this->addFlashMessage('sysmessage.success.templaterecordstore');
                        } else {
                            $this->addFlashMessage('sysmessage.error.templaterecordstore', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
                        }

                        // Re-initialize TemplateService
                        $this->initializeTemplateService($templatePid, $templateUid);
                    }
                } else {
                    $this->addFlashMessage('sysmessage.error.invalidrequestdata', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
                }
            }

            $category = 'fe_cookies';
            $printFields = trim($this->templateService->ext_printFields($this->constants, $category));

            foreach ($this->templateService->getInlineJavaScript() as $name => $inlineJavaScript) {
                $this->objectManager->get(PageRenderer::class)->addJsInlineCode($name, $inlineJavaScript);
            }
        }

        $this->view->assignMultiple([
            'pageUid' => $pageUid,
            'constantsEditor' => $printFields,
            'templateRow' => $this->templateRow,
            'toolbarPartial' => 'Settings',
        ]);
    }

    /**
     * @param array $postData
     */
    protected function checkConstantsFromRequest(array $postData)
    {
        $check = $postData['check'];
        foreach ($check as $constantName => $_ign) {
            if (!$this->isAllowedConstantName($constantName)) {
                return false;
            }
        }
        $data = $postData['data'];
        foreach ($data as $constantName => $_ign) {
            if (!$this->isAllowedConstantName($constantName)) {
                return false;
            }
        }
        return true;
    }

    /**
     * This method is more or less copied from
     * TypoScriptTemplateConstantEditorModuleFunctionController::initialize_editor()
     *
     * @see \TYPO3\CMS\Tstemplate\Controller\TypoScriptTemplateConstantEditorModuleFunctionController::initialize_editor()
     * @param int $pageUid
     * @param int $templateUid
     * @return boolean
     */
    protected function initializeTemplateService($pageUid, $templateUid = 0)
    {
        /** @var ExtendedTemplateService $templateService */
        $this->templateService = GeneralUtility::makeInstance(ExtendedTemplateService::class);
        $this->templateService->init();
        $this->templateRow = $this->templateService->ext_getFirstTemplate($pageUid, $templateUid);
        if (is_array($this->templateRow)) {
            // Get the rootLine
            $rootLine = BackendUtility::BEgetRootLine($pageUid);
            // This generates the constants/config + hierarchy info for the template.
            $this->templateService->runThroughTemplates($rootLine, $templateUid);
            // The editable constants are returned in an array.
            $this->allConstants = $this->templateService->generateConfig_constants();

            $this->constants = [];

            foreach ($this->allConstants as $key => $value) {
                if ($this->isAllowedConstantName($key)) {
                    $this->constants[$key] = $value;
                }
            }

            // The returned constants are sorted in categories, that goes into the $tmpl->categories array
            $this->templateService->ext_categorizeEditableConstants($this->constants);
            // This array will contain key=[expanded constant name], value=line number in template. (after edit_divider, if any)
            $this->templateService->ext_regObjectPositions($this->templateRow['constants']);

            return true;
        }
        return false;
    }

    /**
     * Returns true, when a constant can be edited in the backend module,
     * otherwise false.
     *
     * See TSConfig: mod.fe_cookies.settingsManagement.allowedConstantNames
     *
     * @param string $constantName
     * @throws \InvalidArgumentException
     * @return bool
     */
    protected function isAllowedConstantName($constantName)
    {
        if (!is_string($constantName)) {
            throw new \InvalidArgumentException('$constantName must be string');
        }

        $prefix = 'plugin.tx_fecookies.settings.';
        if (strpos($constantName, $prefix) !== 0) {
            return false;
        }

        $allowedConstantNames = BackendUtility::getPagesTSconfig($pageUid)['mod.']['fe_cookies.']['settingsManagement.']['allowedConstantNames.'] ?? [];

        $return = false;
        foreach ($allowedConstantNames as $allowedConstantName) {
            if ($allowedConstantName === '*') {
                $return = true;
                break;
            }
            $wildcardPos = strpos($allowedConstantName, '*');
            if ($wildcardPos !== false) {
                $parts = explode('*', $allowedConstantName);
                $parts[0] = $prefix . $parts[0];
                array_walk($parts, function(&$v, $k) {
                    $v = preg_quote($v, '~');
                });
                $regex = '~^' . implode('(?:.*?)', $parts) . '$~i';
                if (preg_match($regex, $constantName)) {
                    $return = true;
                    break;
                }
            } elseif ($prefix . $allowedConstantName === $constantName) {
                $return = true;
                break;
            }
        }

        return $return;
    }

    /**
     * @param string $constants
     * @throws \InvalidArgumentException
     * @return bool
     */
    protected function storeTemplateRecord($constants)
    {
        if (!is_string($constants)) {
            throw new \InvalidArgumentException('$constants must be string');
        }
        $templateUid = $this->templateRow['_ORIG_uid'] ?: $this->templateRow['uid'];
        $success = (bool)$this->getConnectionForTable('sys_template')->update(
            'sys_template',
            ['constants' => $constants],
            ['uid' => $templateUid],
            [\PDO::PARAM_STR]
        );
        return $success;
    }

    /**
     * {@inheritDoc}
     * @see \TYPO3\CMS\Extbase\Mvc\Controller\AbstractController::addFlashMessage()
     */
    public function addFlashMessage($messageBody, $messageTitle = '', $severity = \TYPO3\CMS\Core\Messaging\AbstractMessage::OK, $storeInSession = true)
    {
        return parent::addFlashMessage(
            LocalizationUtility::translate($messageBody),
            LocalizationUtility::translate($messageTitle),
            $severity,
            $storeInSession
        );
    }

    /**
     * @todo Replace AbstractMessage::NOTICE with \TYPO3\CMS\Fluid\ViewHelpers\Be\InfoboxViewHelper::STATE_NOTICE when dropping support for TYPO3 < 7.2
     * @param string $message
     * @param string $title
     * @param int $state
     */
    public function infoBoxAction($message = '', $title = '', $state = \TYPO3\CMS\Core\Messaging\AbstractMessage::NOTICE)
    {
        $this->view->assignMultiple([
            'infoBox' => [
                'message' => $message,
                'title' => $title,
                'state' => $state,
            ]
        ]);
    }

    /**
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }

    /**
     * @return bool
     */
    protected function isRequestForPage()
    {
        $pageId = (int)GeneralUtility::_GP('id');
        return $pageId > 0;
    }

    /**
     * @return \TYPO3\CMS\Core\Database\Connection
     */
    protected function getConnectionForTable($tableName)
    {
        return GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)->getConnectionForTable($tableName);
    }
}
