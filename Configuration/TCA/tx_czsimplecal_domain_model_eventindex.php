<?php
// This TCA is needed to get the Domain Object Mapper of Extbase to work,
// but this table should not be displayed in the Frontent.
return [
    'ctrl' => [
        'title' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_eventindex',
        'label' => '',
        'hideTable' => true,
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('cz_simple_cal')
            . 'Resources/Public/Icons/tx_czsimplecal_domain_model_event.gif',
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
                'eval' => 'datetime,required',
            ],
        ],
        'end' => [
            'exclude' => 0,
            'label' => '',
            'config' => [
                'type' => 'input',
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
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => [
                    [
                        'LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages',
                        -1,
                    ],
                    [
                        'LLL:EXT:lang/locallang_general.xlf:LGL.default_value',
                        0,
                    ],
                ],
            ],
        ],
        'teaser' => [
            'exclude' => 0,
            'label' => '',
            'config' => ['type' => 'none'],
        ],
    ],
];
