<?php
/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

return [
    'ctrl' => [
        'title' => 'LLL:EXT:fe_cookies/Resources/Private/Language/locallang_db.xlf:tca.tx_fecookies_domain_model_block',
        'label' => 'title',
        'label_alt' => 'bodytext',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'sortby' => 'sorting',
        'delete' => 'deleted',
        'editlock' => 'editlock',
        'type' => 'type',
        'hideAtCopy' => true,
        'prependAtCopy' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.prependAtCopy',
        'copyAfterDuplFields' => 'sys_language_uid',
        'transOrigPointerField' => 'l18n_parent',
        'transOrigDiffSourceField' => 'l18n_diffsource',
        'languageField' => 'sys_language_uid',
        'translationSource' => 'l10n_source',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
            'fe_group' => 'fe_group',
        ],
        'searchFields' => 'title, bodytext',
        'iconfile' => 'EXT:fe_cookies/Resources/Public/Icons/Block.svg',
    ],
    'interface' => [
        'showRecordFieldList' => 'title, bodytext, sys_language_uid, starttime, endtime, fe_group',
    ],
    'columns' => [
        'hidden' => $GLOBALS['TCA']['tt_content']['columns']['hidden'],
        'starttime' => $GLOBALS['TCA']['tt_content']['columns']['starttime'],
        'endtime' => $GLOBALS['TCA']['tt_content']['columns']['endtime'],
        'fe_group' => $GLOBALS['TCA']['tt_content']['columns']['fe_group'],
        'editlock' => $GLOBALS['TCA']['tt_content']['columns']['editlock'],
        'sys_language_uid' => $GLOBALS['TCA']['tt_content']['columns']['sys_language_uid'],
        'l10n_source' => $GLOBALS['TCA']['tt_content']['columns']['l10n_source'],
        'l18n_parent' => [
            'exclude' => true,
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        '',
                        0
                    ]
                ],
                'foreign_table' => 'tx_fecookies_domain_model_block',
                'foreign_table_where' => 'AND tx_fecookies_domain_model_block.pid=###CURRENT_PID### AND tx_fecookies_domain_model_block.sys_language_uid IN (-1,0)',
                'default' => 0
            ]
        ],
        'type' => [
            'exclude' => true,
            'label' => 'LLL:EXT:fe_cookies/Resources/Private/Language/locallang_db.xlf:tca.tx_fecookies_domain_model_block.type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:fe_cookies/Resources/Private/Language/locallang_db.xlf:tca.tx_fecookies_domain_model_block.type.text',
                        'text'
                    ],
                ],
            ],
        ],
        'title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:fe_cookies/Resources/Private/Language/locallang_db.xlf:tca.tx_fecookies_domain_model_block.title',
            'l10n_mode' => 'prefixLangTitle',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
                'eval' => 'trim,required',
            ],
        ],
        'bodytext' => [
            'exclude' => true,
            'label' => 'LLL:EXT:fe_cookies/Resources/Private/Language/locallang_db.xlf:tca.tx_fecookies_domain_model_block.bodytext',
            'l10n_mode' => 'prefixLangTitle',
            'config' => [
                'type' => 'text',
                'cols' => '80',
                'rows' => '15',
                'softref' => 'typolink_tag,images,email[subst],url',
                'eval' => 'trim',
            ],
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    type, title, bodytext,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    --palette--;;hidden,
                    --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                    --palette--;;language,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
            ',
            'columnsOverrides' => [
                'bodytext' => [
                    'config' => [
                        'enableRichtext' => true,
                        'richtextConfiguration' => 'default',
                    ],
                ],
            ],
        ],
    ],
    'palettes' => [
        'hidden' => [
            'showitem' => '
                hidden;LLL:EXT:fe_cookies/Resources/Private/Language/locallang_db.xlf:tca.tx_fecookies_domain_model_block.palette.hidden
            ',
        ],
        'language' => [
            'showitem' => '
                sys_language_uid;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:sys_language_uid_formlabel,l18n_parent
            ',
        ],
        'access' => [
            'showitem' => '
                starttime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel,
                endtime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel,
                --linebreak--,
                fe_group;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:fe_group_formlabel,
                --linebreak--,editlock
            ',
        ],
    ],
];
