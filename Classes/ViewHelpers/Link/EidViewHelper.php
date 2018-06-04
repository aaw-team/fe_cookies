<?php
namespace AawTeam\FeCookies\ViewHelpers\Link;

/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * EidViewHelper
 */
class EidViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'a';

    /**
     * {@inheritDoc}
     * @see \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper::initializeArguments()
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerTagAttribute('target', 'string', 'Target of link', false);
        $this->registerTagAttribute('rel', 'string', 'Specifies the relationship between the current document and the linked document', false);
    }

    /**
     * @return string
     */
    public function render()
    {
        $requestUri = GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL');
        $salt = bin2hex($this->getRandomBytes(32));
        $challenge = \hash_hmac('sha256', $salt . $requestUri, $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']);

        /** @var \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder $uriBuilder */
        $uriBuilder = $this->renderingContext->getControllerContext()->getUriBuilder();
        $uri = (string)$uriBuilder->reset()
            ->setArguments([
                'eID' => 'fecookies',
                'returnUrl' => $requestUri,
                'salt' => $salt,
                'challenge' => $challenge,
            ])
            ->setUseCacheHash(false)
            ->build()
        ;
        if ($uri !== '') {
            $this->tag->addAttribute('href', $uri);
            $this->tag->setContent($this->renderChildren());
            $result = $this->tag->render();
        } else {
            $result = $this->renderChildren();
        }
        return $result;
    }

    /**
     * This method must stay in place as long as PHP < 7 is supported
     * and, thus, paragonie/random_compat is required.
     *
     * @param int $bytes
     * @return string
     */
    private function getRandomBytes($bytes)
    {
        try {
            return \random_bytes($bytes);
        } catch (\Exception $e) {
        }
        return GeneralUtility::generateRandomBytes($bytes);
    }
}
