<?php
declare(strict_types=1);

return [
    'ctrl' => [
        'title' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_category',
        'label' => 'title',
        'default_sortby' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l18n_parent',
        'transOrigDiffSourceField' => 'l18n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => ['disabled' => 'hidden'],
        'iconfile' => 'EXT:cz_simple_cal/Resources/Public/Icons/tx_czsimplecal_domain_model_category.gif',
    ],
    'interface' => ['showRecordFieldList' => 'title,slug,color,show_page_instead'],
    'types' => [
        '1' => ['showitem' => 'title,slug,color,show_page_instead'],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple',
                    ],
                ],
                'default' => 0,
            ],
        ],
        'l18n_parent' => [
            'exclude' => true,
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        '',
                        0,
                    ],
                ],
                'foreign_table' => 'tx_czsimplecal_domain_model_category',
                'foreign_table_where' => 'AND tx_czsimplecal_domain_model_category.uid=###REC_FIELD_l18n_parent### AND tx_czsimplecal_domain_model_category.sys_language_uid IN (-1,0)',
                'default' => 0,
            ],
        ],
        'l18n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
                'default' => '',
            ],
        ],
        't3ver_label' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => true,
                    ],
                ],
            ],
        ],
        'color' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_category.color',
            'config' => [
                'type' => 'input',
                'renderType' => 'colorpicker',
            ],
        ],
        'slug' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_category.slug',
            'config' => [
                'type' => 'slug',
                'size' => 50,
                'generatorOptions' => [
                    'fields' => ['title'],
                    'replacements' => ['/' => '-'],
                ],
                'fallbackCharacter' => '-',
                'eval' => 'uniqueInSite',
                'default' => '',
            ],
        ],
        'title' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_category.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'show_page_instead' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_category.show_page_instead',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'pages',
                'size' => 1,
                'maxitems' => 1,
                'minitems' => 0,
                'suggestOptions' => [
                    'default' => ['additionalSearchFields' => 'nav_title, alias, url'],
                ],
                'default' => 0,
            ],
        ],
    ],
];
