<?php
declare(strict_types=1);

// This TCA is needed to get the Domain Object Mapper of Extbase to work,
// but this table should not be displayed in the Frontent.
return [
    'ctrl' => [
        'title' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_eventindex',
        'label' => '',
        'hideTable' => true,
        'iconfile' => 'EXT:cz_simple_cal/Resources/Public/Icons/tx_czsimplecal_domain_model_event.gif',
        'languageField' => 'sys_language_uid',
    ],
    'interface' => ['showRecordFieldList' => ''],
    'types' => [
        '1' => ['showitem' => ''],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
    'columns' => [
        'pid' => [
            'exclude' => 0,
            'label' => '',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'pages',
                'minitems' => 1,
                'maxitems' => 1,
            ],
        ],
        'start' => [
            'exclude' => 0,
            'label' => '',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,required',
            ],
        ],
        'end' => [
            'exclude' => 0,
            'label' => '',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,required',
            ],
        ],
        'event' => [
            'exclude' => 0,
            'label' => '',
            'config' => [
                'type' => 'passthrough',
                'foreign_table' => 'tx_czsimplecal_domain_model_event',
            ],
        ],
        'slug' => [
            'exclude' => 0,
            'label' => '',
            'config' => ['type' => 'none'],
        ],
        'status' => [
            'exclude' => 0,
            'label' => '',
            'config' => [
                'type' => 'input',
                'eval' => 'null',
                'default' => null,
            ],
        ],
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
        'teaser' => [
            'exclude' => 0,
            'label' => '',
            'config' => ['type' => 'none'],
        ],
    ],
];
