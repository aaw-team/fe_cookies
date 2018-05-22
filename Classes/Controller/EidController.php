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
        $returnUrl = $queryParams['returnUrl'] ?: null;
        $salt = $queryParams['salt'] ?: null;
        $challenge = $queryParams['challenge'] ?: null;
        if (
            $returnUrl === null || !is_string($returnUrl) || empty($returnUrl)
            || $salt === null || !is_string($salt) || empty($salt)
            || $challenge === null || !is_string($challenge) || empty($challenge)
        ) {
            //$response->getBody()->write('<h1>400 Bad request</h1><p>The server encountered bad arguments.</p>');
            return $response->withStatus(400, 'Bad Request');
        }

        // Try to verify the challenge
        $hash = \hash_hmac('sha256', $salt . $returnUrl, $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']);
        if (!hash_equals($hash, $challenge)) {
            return $response->withStatus(400, 'Bad Request');
        }

        $this->setCookie();

        return $response
            ->withStatus(307, 'Temporary Redirect')
            ->withHeader('Location', $returnUrl)
            ->withHeader('X-FeCookie-Set', '1')
        ;
    }

    /**
     * @return bool
     */
    protected function setCookie()
    {
        return FeCookiesUtility::setCookie();
    }
}
