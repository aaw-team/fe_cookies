<?php
/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

$EM_CONF[$_EXTKEY] = [
    'title' => 'Frontend cookies',
    'description' => 'Provides a cookie consent plugin for the frontend, a backend module for convenient cookie-banner management, simple but powerful APIs (PHP, JavaScript and CSS) and lots of configuration possibilities, including separate cookie settings per domain.',
    'category' => 'fe',
    'author' => 'Agentur am Wasser | Maeder & Partner AG',
    'author_email' => 'development@agenturamwasser.ch',
    'state' => 'beta',
    'clearCacheOnLoad' => true,
    'version' => '1.0.0-dev',
    'constraints' => [
        'depends' => [
            'php' => '7.2',
            'typo3' => '9.5.0-10.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
