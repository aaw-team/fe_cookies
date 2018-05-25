<?php
namespace AawTeam\FeCookies\Controller;

/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use AawTeam\FeCookies\Utility\FeCookiesUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;

/**
 * EidController
 */
class EidController
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function mainAction(ServerRequestInterface $request, ResponseInterface $response)
    {
        // Analyze query params
        $queryParams = $request->getQueryParams();
        if (!$this->checkQueryParams($queryParams)) {
            return $response->withStatus(400, 'Bad Request');
        }
        $returnUrl = $queryParams['returnUrl'];
        $salt = $queryParams['salt'];
        $challenge = $queryParams['challenge'];

        // Try to verify the challenge
        if (!$this->verifyChallenge($challenge, $salt, $returnUrl)) {
            return $response->withStatus(400, 'Bad Request');
        }

        // Set the cookie
        FeCookiesUtility::setCookie();

        return $response
            ->withStatus(307, 'Temporary Redirect')
            ->withHeader('Location', $returnUrl)
            ->withHeader('X-FeCookie-Set', '1')
        ;
    }

    /**
     *
     */
    public function legacyAction()
    {
        $queryParams = GeneralUtility::_GET();
        if (!$this->checkQueryParams($queryParams)) {
            HttpUtility::setResponseCodeAndExit(HttpUtility::HTTP_STATUS_400);
            die();
        }
        $returnUrl = $queryParams['returnUrl'];
        $salt = $queryParams['salt'];
        $challenge = $queryParams['challenge'];

        // Try to verify the challenge
        if (!$this->verifyChallenge($challenge, $salt, $returnUrl)) {
            HttpUtility::setResponseCodeAndExit(HttpUtility::HTTP_STATUS_400);
            die();
        }

        // Set the cookie
        FeCookiesUtility::setCookie();

        header('X-FeCookie-Set: 1');
        header('Location: ' . $returnUrl);
        HttpUtility::setResponseCodeAndExit(HttpUtility::HTTP_STATUS_307);
    }

    /**
     * @param string $challenge
     * @param string $salt
     * @param string $returnUrl
     * @return bool
     */
    protected function verifyChallenge($challenge, $salt, $returnUrl)
    {
        $hash = \hash_hmac('sha256', $salt . $returnUrl, $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']);
        return \hash_equals($hash, $challenge);
    }

    /**
     * @param array $queryParams
     * @return bool
     */
    protected function checkQueryParams(array $queryParams)
    {
        $returnUrl = $queryParams['returnUrl'] ?: null;
        $salt = $queryParams['salt'] ?: null;
        $challenge = $queryParams['challenge'] ?: null;
        if (
            $returnUrl === null || !is_string($returnUrl) || empty($returnUrl)
            || $salt === null || !is_string($salt) || empty($salt)
            || $challenge === null || !is_string($challenge) || empty($challenge)
        ) {
            return false;
        }
        return true;
    }
}
