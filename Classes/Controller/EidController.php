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
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * EidController
 */
class EidController
{
    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function mainAction(ServerRequestInterface $request): ResponseInterface
    {
        // Analyze query params
        $queryParams = $request->getQueryParams();
        if (!$this->checkQueryParams($queryParams)) {
            return $this->createHttpResponse(400);
        }
        $returnUrl = $queryParams['returnUrl'];
        $salt = $queryParams['salt'];
        $challenge = $queryParams['challenge'];

        // Try to verify the challenge
        if (!$this->verifyChallenge($challenge, $salt, $returnUrl)) {
            return $this->createHttpResponse(400);
        }

        $response = $this->createHttpResponse(307)
            ->withHeader('Location', $returnUrl)
            ->withHeader('X-FeCookie-Set', '1');
        return FeCookiesUtility::addCookie($response);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function legacyAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->mainAction($request);
    }

    /**
     * Pretty much like PSR-17 ResponseFactory::createResponse().
     *
     * @param int $code
     * @param string $reasonPhrase
     * @return ResponseInterface
     */
    protected function createHttpResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return GeneralUtility::makeInstance(Response::class)->withStatus($code, $reasonPhrase);
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
