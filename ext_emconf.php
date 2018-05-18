<?php
/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Frontend cookies',
    'description' => '',
    'category' => 'fe',
    'author' => 'Agentur am Wasser | Maeder & Partner AG',
    'author_email' => 'development@agenturamwasser.ch',
    'state' => 'beta',
    'clearCacheOnLoad' => true,
    'version' => '0.1.0-dev',
    'constraints' => array(
        'depends' => array(
            'php' => '5.6.0-7.2.999',
            'typo3' => '7.6.27-8.7.999',
        ),
        'conflicts' => array(),
        'suggests' => array(),
    ),
);
