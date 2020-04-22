<?php
namespace AawTeam\FeCookies\ViewHelpers\Link;

/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * EidViewHelper
 */
class EidViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'a';

    /**
     * {@inheritDoc}
     * @see AbstractTagBasedViewHelper::initializeArguments()
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
        $salt = substr(hash('sha256', random_bytes(32)), -32);
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
}
