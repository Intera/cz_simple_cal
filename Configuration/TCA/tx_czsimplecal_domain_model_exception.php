<?php
declare(strict_types=1);

use Tx\CzSimpleCal\Domain\Model\Enumeration\EventStatus;
use Tx\CzSimpleCal\Domain\Model\Enumeration\ExceptionType;

$languagePrefix = 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:';
$languagePrefixColumn = $languagePrefix . 'tx_czsimplecal_domain_model_exception.';
$commonFields = 'type, title, start_day, start_time, end_day, end_time, --palette--;;system,
	--div--;' . $languagePrefixColumn . 'tab_recurrance,
	recurrance_type, recurrance_subtype, recurrance_until';
return [
    'ctrl' => [
        'title' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_exception',
        'label' => 'title',
        'delete' => 'deleted',
        'hideTable' => true,
        'type' => 'type',
        'enablecolumns' => ['disabled' => 'hidden'],
        'iconfile' => 'EXT:cz_simple_cal/Resources/Public/Icons/tx_czsimplecal_domain_model_exception.gif',
        'dividers2tabs' => 1,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
    ],
    'interface' => ['showRecordFieldList' => 'type,title,start_day,start_time,end_day,end_time,recurrance_type,recurrance_subtype,recurrance_until,status,teaser'],
    'types' => [
        '0' => ['showitem' => 'type'],
        ExceptionType::HIDE_EVENT => ['showitem' => $commonFields],
        ExceptionType::UPDATE_EVENT => [
            'showitem' => '' .
                $commonFields . ',
                --div--;' . $languagePrefixColumn . 'tab_update_event_properties,
			        status, teaser
		    ',
        ],
    ],
    'palettes' => [
        'system' => [
            'showitem' => 'hidden, sys_language_uid, l10n_parent',
            'isHiddenPalette' => true,
        ],
    ],
    'columns' => [
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
        'title' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_exception.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
            ],
        ],
        'start_day' => [
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
            'exclude' => 1,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.start_day',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 12,
                'eval' => 'date,required',
            ],
        ],
        'start_time' => [
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
            'exclude' => 1,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.start_time',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 12,
                'eval' => 'null,time',
                'default' => null,
            ],
        ],
        'end_day' => [
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
            'exclude' => 1,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.end_day',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 12,
                'eval' => 'null,date',
                'default' => null,
            ],
        ],
        'end_time' => [
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
            'exclude' => 1,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.end_time',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 12,
                'eval' => 'null,time',
                'default' => null,
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
                'default' => '',
            ],
        ],
        'l10n_parent' => [
            'exclude' => false,
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
                'foreign_table' => 'tx_czsimplecal_domain_model_exception',
                'foreign_table_where' => 'AND tx_czsimplecal_domain_model_exception.uid=###REC_FIELD_l10n_parent### AND tx_czsimplecal_domain_model_exception.sys_language_uid IN (-1,0)',
                'default' => 0,
            ],

        ],
        'timezone' => [
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
            'exclude' => 1,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.timezone',
            'config' => [
                'type' => 'input',
                'size' => 40,
                'max' => 40,
                'eval' => 'string',
                'default' => 'GMT',
            ],
        ],
        'recurrance_type' => [
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
            'exclude' => 1,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.recurrance_type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.recurrance_type.none',
                        'none',
                    ],
                    [
                        'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.recurrance_type.daily',
                        'daily',
                    ],
                    [
                        'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.recurrance_type.weekly',
                        'weekly',
                    ],
                    [
                        'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.recurrance_type.monthly',
                        'monthly',
                    ],
                    [
                        'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.recurrance_type.yearly',
                        'yearly',
                    ],
                ],
            ],
        ],
        'recurrance_subtype' => [
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
            'exclude' => 1,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.recurrance_subtype',
            'displayCond' => 'FIELD:recurrance_type:!IN:0,,none,daily',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'itemsProcFunc' => 'Tx\\CzSimpleCal\\Utility\\EventConfig->getRecurranceSubtype',
            ],
        ],
        'recurrance_until' => [
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
            'exclude' => 1,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.recurrance_until',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 12,
                'eval' => 'null,date',
                'default' => null,
            ],
        ],
        'status' => [
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
            'exclude' => 1,
            'label' => $languagePrefixColumn . 'status',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        $languagePrefixColumn . 'status.I.undefined',
                        EventStatus::UNDEFINED,
                    ],
                    [
                        $languagePrefixColumn . 'status.I.tentative',
                        EventStatus::TENTATIVE,
                    ],
                    [
                        $languagePrefixColumn . 'status.I.confirmed',
                        EventStatus::CONFIRMED,
                    ],
                    [
                        $languagePrefixColumn . 'status.I.cancelled',
                        EventStatus::CANCELLED,
                    ],
                ],
                'default' => EventStatus::CONFIRMED,
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
            'exclude' => 1,
            'label' => $languagePrefixColumn . 'teaser',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 6,
                'eval' => 'null,trim',
                'default' => null,
            ],
        ],
        'type' => [
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
            'exclude' => 0,
            'label' => $languagePrefixColumn . 'type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        $languagePrefixColumn . 'type.I.hide_event',
                        ExceptionType::HIDE_EVENT,
                    ],
                    [
                        $languagePrefixColumn . 'type.I.update_event',
                        ExceptionType::UPDATE_EVENT,
                    ],
                ],
                'default' => ExceptionType::HIDE_EVENT,
            ],
        ],
    ],
];
