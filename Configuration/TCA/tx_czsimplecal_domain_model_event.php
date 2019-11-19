<?php
declare(strict_types=1);

use Tx\CzSimpleCal\Domain\Model\Enumeration\RecurranceType;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

return [
    'ctrl' => [
        'title' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => 'ORDER BY start_day DESC, start_time DESC',
        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l18n_parent',
        'transOrigDiffSourceField' => 'l18n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'endtime' => 'enable_endtime',
        ],
        'dividers2tabs' => 1,
        'iconfile' => 'EXT:cz_simple_cal/Resources/Public/Icons/tx_czsimplecal_domain_model_event.gif',
    ],
    'interface' => [
        'showRecordFieldList' => 'title,slug,start_day,start_time,end_day,end_time,teaser,description,recurrance_type'
            . ',recurrance_subtype,recurrance_until,location_inline,location,organizer_inline,organizer,categories'
            . ',show_page_instead,exceptions,exception_groups,flickr_tags,twitter_hashtags',
    ],
    'types' => [
        '1' => [
            'showitem' => '
                --div--;LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.tab_general,
                    title, slug, palette;;start, palette;;end, status, categories, show_page_instead, teaser, description,
                --div--;LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.tab_resources,
                    images, files, event_languages,
                --div--;LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.tab_recurrance,
                    recurrance_type,recurrance_subtype,recurrance_until,exceptions,exception_groups,
                --div--;LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.tab_location,
                    location_inline,location,
                --div--;LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.tab_organizer,
                    organizer_inline,organizer,
                --div--;LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.tab_socialmedia,
    			    twitter_hashtags,flickr_tags,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    --palette--;;access
            ',
        ],
    ],
    'palettes' => [
        'start' => ['showitem' => 'start_day,start_time'],
        'end' => ['showitem' => 'end_day,end_time'],
        'access' => [
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access',
            'showitem' => 'hidden, enable_endtime',
        ],
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
                'foreign_table' => 'tx_czsimplecal_domain_model_event',
                'foreign_table_where' => 'AND tx_czsimplecal_domain_model_event.uid=###REC_FIELD_l18n_parent### AND tx_czsimplecal_domain_model_event.sys_language_uid IN (-1,0)',
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
        'deleted' => [
            'config' => ['type' => 'passthrough'],
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
        'tstamp' => [
            'config' => ['type' => 'passthrough'],
        ],
        'title' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.title',
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
        'enable_endtime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 13,
                'eval' => 'datetime',
                'default' => '0',
            ],
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
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
        'timezone' => [
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
        'teaser' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.teaser',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 6,
                'eval' => 'trim',
            ],
        ],
        'description' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.description',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'cols' => 40,
                'rows' => 6,
            ],
        ],
        'images' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.images',
            'config' => ExtensionManagementUtility::getFileFieldTCAConfig(
                'images',
                [
                    'appearance' => ['createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:images.addFileReference'],
                    // Custom configuration for displaying fields in the overlay/reference table
                    // to use the imageoverlayPalette instead of the basicoverlayPalette
                    'overrideChildTca' => [
                        'types' => [
                            '0' => [
                                'showitem' => '
						        --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
						        --palette--;;filePalette
						    ',
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => [
                                'showitem' => '
						        --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
						        --palette--;;filePalette
						    ',
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                                'showitem' => '
						        --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
						        --palette--;;filePalette
						    ',
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => [
                                'showitem' => '
						        --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
						        --palette--;;filePalette
						    ',
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => [
                                'showitem' => '
						        --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
						        --palette--;;filePalette
						    ',
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => [
                                'showitem' => '
						        --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
						        --palette--;;filePalette
						    ',
                            ],
                        ],
                    ],
                ],
                $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
            ),
        ],
        'files' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.files',
            'config' => ExtensionManagementUtility::getFileFieldTCAConfig(
                'files',
                [
                    'appearance' => ['createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:media.addFileReference'],
                ]
            ),
        ],
        'recurrance_type' => [
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
            'exclude' => 1,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.recurrance_type',
            'onChange' => 'reload',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.recurrance_type.none',
                        RecurranceType::NONE,
                    ],
                    [
                        'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.recurrance_type.daily',
                        RecurranceType::DAILY,
                    ],
                    [
                        'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.recurrance_type.weekly',
                        RecurranceType::WEEKLY,
                    ],
                    [
                        'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.recurrance_type.monthly',
                        RecurranceType::MONTHLY,
                    ],
                    [
                        'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.recurrance_type.yearly',
                        RecurranceType::YEARLY,
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
        'location_inline' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.location',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_czsimplecal_domain_model_address',
                'foreign_field' => 'event_uid',
                'foreign_match_fields' => ['event_field' => 'location_inline'],
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
                'appearance' => [
                    'showPossibleLocalizationRecords' => true,
                    'showRemovedLocalizationRecords' => true,
                    'showAllLocalizationLink' => true,
                    'showSynchronizationLink' => true,
                ],
            ],
        ],
        'location' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.location',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
                'allowed' => 'tx_czsimplecal_domain_model_address',
            ],
        ],
        'organizer_inline' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.organizer',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_czsimplecal_domain_model_address',
                'foreign_field' => 'event_uid',
                'foreign_match_fields' => ['event_field' => 'organizer_inline'],
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
                'appearance' => [
                    'showPossibleLocalizationRecords' => true,
                    'showRemovedLocalizationRecords' => true,
                    'showAllLocalizationLink' => true,
                    'showSynchronizationLink' => true,
                ],
            ],
        ],
        'organizer' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.organizer',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
                'allowed' => 'tx_czsimplecal_domain_model_address',
            ],
        ],
        'categories' => [
            'l10n_mode' => 'exclude',
            'exclude' => 0,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.categories',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'enableMultiSelectFilterTextfield' => true,
                'foreign_table' => 'tx_czsimplecal_domain_model_category',
                'MM' => 'tx_czsimplecal_event_category_mm',
            ],
        ],
        'show_page_instead' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.show_page_instead',
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
        'exceptions' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.exceptions',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_czsimplecal_domain_model_exception',
                'foreign_field' => 'parent_uid',
                'foreign_table_field' => 'parent_table',
                'foreign_match_fields' => ['parent_field' => 'exceptions'],
                'appearance' => [
                    'showPossibleLocalizationRecords' => true,
                    'showRemovedLocalizationRecords' => true,
                    'showAllLocalizationLink' => true,
                    'showSynchronizationLink' => true,
                ],
            ],
        ],
        'exception_groups' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.exception_groups',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_czsimplecal_domain_model_exceptiongroup',
                'MM' => 'tx_czsimplecal_event_exceptiongroup_mm',
                // The foreign_table config is required for Extbase.
                'foreign_table' => 'tx_czsimplecal_domain_model_exceptiongroup',
                'maxitems' => 99999,
                'size' => 5,
                'autoSizeMax' => 20,
            ],
        ],
        'twitter_hashtags' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.twitter_hashtags',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'eval' => 'trim',
            ],
        ],
        'flickr_tags' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.flickr_tags',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'eval' => 'trim',
            ],
        ],
        'status' => [
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
            'exclude' => 1,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.status',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.status.tentative',
                        'TENTATIVE',
                    ],
                    [
                        'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.status.confirmed',
                        'CONFIRMED',
                    ],
                    [
                        'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.status.cancelled',
                        'CANCELLED',
                    ],
                ],
                'default' => 'CONFIRMED',
            ],
        ],
        'slug' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.slug',
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
        'last_indexed' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.last_indexed',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 12,
                'eval' => 'date',
            ],
        ],
        'cruser_fe' => [
            'exclude' => 1,
            'config' => ['type' => 'none'],
        ],
        'crgroup_fe' => [
            'exclude' => 1,
            'config' => ['type' => 'none'],
        ],
    ],
];
