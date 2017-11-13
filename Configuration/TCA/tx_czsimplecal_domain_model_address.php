<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_address',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'versioningWS' => 2,
        'versioning_followPages' => true,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l18n_parent',
        'transOrigDiffSourceField' => 'l18n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => ['disabled' => 'hidden'],
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('cz_simple_cal')
            . 'Resources/Public/Icons/tx_czsimplecal_domain_model_address.gif',
    ],
    'interface' => ['showRecordFieldList' => 'name,address,zip,city,country'],
    'types' => [
        '1' => ['showitem' => 'name,address,zip,city,country,homepage'],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.php:LGL.language',
            'config' => [
                'type' => 'select',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => [
                    [
                        'LLL:EXT:lang/locallang_general.php:LGL.allLanguages',
                        -1,
                    ],
                    [
                        'LLL:EXT:lang/locallang_general.php:LGL.default_value',
                        0,
                    ],
                ],
            ],
        ],
        'l18n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'items' => [
                    [
                        '',
                        0,
                    ],
                ],
                'foreign_table' => 'tx_czsimplecal_domain_model_category',
                'foreign_table_where' => 'AND tx_czsimplecal_domain_model_category.uid=###REC_FIELD_l18n_parent### AND tx_czsimplecal_domain_model_category.sys_language_uid IN (-1,0)',
            ],
        ],
        'l18n_diffsource' => [
            'config' => ['type' => 'passthrough'],
        ],
        't3ver_label' => [
            'displayCond' => 'FIELD:t3ver_label:REQ:true',
            'label' => 'LLL:EXT:lang/locallang_general.php:LGL.versionLabel',
            'config' => [
                'type' => 'none',
                'cols' => 27,
            ],
        ],
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config' => ['type' => 'check'],
        ],
        'homepage' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_address.homepage',
            'config' => [
                'eval' => 'trim',
                'max' => 256,
                'size' => 25,
                'softref' => 'typolink',
                'type' => 'input',
                'wizards' => [
                    'link' => [
                        'type' => 'popup',
                        'title' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_link_formlabel',
                        'icon' => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_link.gif',
                        'module' => ['name' => 'wizard_link'],
                        'JSopenParams' => 'width=800,height=600,status=0,menubar=0,scrollbars=1',
                    ],
                ],
            ],
        ],
        'name' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_address.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'eval' => 'trim,required',
            ],
        ],
        'address' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_address.address',
            'config' => [
                'type' => 'text',
                'cols' => 20,
                'rows' => 4,
                'eval' => 'trim',
            ],
        ],
        'zip' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_address.zip',
            'config' => [
                'type' => 'input',
                'size' => 7,
                'max' => 12,
                'eval' => 'trim',
            ],
        ],
        'city' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_address.city',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'eval' => 'trim',
            ],
        ],
        'country' => [
            'displayCond' => 'EXT:static_info_tables:LOADED:TRUE',
            'exclude' => 1,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_address.country',
            'config' => [
                'type' => 'select',
                'items' => [
                    [
                        '',
                        0,
                    ],
                ],
                'itemsProcFunc' => 'tx_staticinfotables_div->selectItemsTCA',
                'itemsProcFunc_config' => [
                    'table' => 'static_countries',
                    'indexField' => 'cn_iso_3',
                    'prependHotlist' => 1,
                ],
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
    ],
];
