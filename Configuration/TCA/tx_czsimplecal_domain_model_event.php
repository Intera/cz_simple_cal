<?php
return array(
	'ctrl' => array(
		'title' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'default_sortby' => 'ORDER BY start_day DESC, start_time DESC',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l18n_parent',
		'transOrigDiffSourceField' => 'l18n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'endtime' => 'enable_endtime',
		),
		'dividers2tabs' => 1,
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('cz_simple_cal') . 'Resources/Public/Icons/tx_czsimplecal_domain_model_event.gif',
		'requestUpdate' => 'recurrance_type',
	),
	'interface' => array(
		'showRecordFieldList' => 'title,start_day,start_time,end_day,end_time,teaser,description,slug,recurrance_type,recurrance_subtype,recurrance_until,location_inline,location,organizer_inline,organizer,categories,show_page_instead,exceptions,exception_groups,flickr_tags,twitter_hashtags'
	),
	'types' => array(
		'1' => array('showitem' => '
			--div--;LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.tab_general,
			title,start_day,start_time,end_day,end_time,status,categories,show_page_instead,teaser,description;;;richtext:rte_transform[flag=rte_enabled|mode=ts_css],
			--div--;LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.tab_resources,
			images,files,slug,
			--div--;LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.tab_recurrance,
			recurrance_type,recurrance_subtype,recurrance_until,exceptions,exception_groups,
			--div--;LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.tab_location,
			location_inline,location,
			--div--;LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.tab_organizer,
			organizer_inline,organizer,
			--div--;LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.tab_socialmedia,
			twitter_hashtags,flickr_tags,
			--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access,
				--palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.access;access'
		)
	),
	'palettes' => array(
		'access' => array(
			'showitem' => 'hidden, enable_endtime',
			'canNotCollapse' => 1
		),
	),
	'columns' => array(
		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0)
				)
			)
		),
		'l18n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_czsimplecal_domain_model_event',
				'foreign_table_where' => 'AND tx_czsimplecal_domain_model_event.uid=###REC_FIELD_l18n_parent### AND tx_czsimplecal_domain_model_event.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough'
			)
		),
		't3ver_label' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'max' => '255'
			)
		),
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check'
			)
		),
		'deleted' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.deleted',
			'config' => array(
				'type' => 'check'
			)
		),
		'pid' => array(
			'exclude' => 0,
			'label' => '',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'pages',
				'minitems' => 1,
				'maxitems' => 1
			)
		),
		'tstamp' => array(
			'config' => array(
				'type' => 'passthrough',
			)
		),
		'title' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.title',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			)
		),
		'start_day' => array(
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'exclude' => 1,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.start_day',
			'config' => array(
				'type' => 'input',
				'size' => 12,
				'max' => 20,
				'eval' => 'date,required',
			)
		),
		'start_time' => array(
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'exclude' => 1,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.start_time',
			'config' => array(
				'type' => 'input',
				'size' => 12,
				'max' => 20,
				'eval' => 'null,time',
				'default' => NULL,
			)
		),
		'enable_endtime' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => '13',
				'max' => '20',
				'eval' => 'datetime',
				'default' => '0',
			),
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly'
		),
		'end_day' => array(
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'exclude' => 1,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.end_day',
			'config' => array(
				'type' => 'input',
				'size' => 12,
				'max' => 20,
				'eval' => 'null,date',
				'default' => NULL,
			)
		),
		'end_time' => array(
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'exclude' => 1,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.end_time',
			'config' => array(
				'type' => 'input',
				'size' => 12,
				'max' => 20,
				'eval' => 'null,time',
				'default' => NULL,
			)
		),
		'timezone' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.timezone',
			'config' => array(
				'type' => 'input',
				'size' => 40,
				'max' => 40,
				'eval' => 'string',
				'default' => 'GMT'
			)
		),
		'teaser' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.teaser',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 6,
				'eval' => 'trim'
			)
		),
		'description' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.description',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 6,
				'wizards' => array(
					'_PADDING' => 4,
					'RTE' => array(
						'notNewRecords' => 1,
						'RTEonly' => 1,
						'type' => 'script',
						'title' => 'LLL:EXT:cms/locallang_ttc.php:bodytext.W.RTE',
						'icon' => 'wizard_rte2.gif',
						'script' => 'wizard_rte.php',
					),
				)
			)
		),
		'images' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.images',
			'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('images', array(
					'appearance' => array(
						'createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:images.addFileReference'
					),
					// custom configuration for displaying fields in the overlay/reference table
					// to use the imageoverlayPalette instead of the basicoverlayPalette
					'foreign_types' => array(
						'0' => array(
							'showitem' => '
						--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
						--palette--;;filePalette'
						),
						\TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => array(
							'showitem' => '
						--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
						--palette--;;filePalette'
						),
						\TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => array(
							'showitem' => '
						--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
						--palette--;;filePalette'
						),
						\TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => array(
							'showitem' => '
						--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
						--palette--;;filePalette'
						),
						\TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => array(
							'showitem' => '
						--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
						--palette--;;filePalette'
						),
						\TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => array(
							'showitem' => '
						--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
						--palette--;;filePalette'
						)
					)
				), $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'])
		),
		'files' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.files',
			'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('files', array(
					'appearance' => array(
						'createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:media.addFileReference'
					)
				))
		),
		'recurrance_type' => array(
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'exclude' => 1,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.recurrance_type',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.recurrance_type.none',
						\Tx\CzSimpleCal\Domain\Model\Enumeration\RecurranceType::NONE
					),
					array(
						'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.recurrance_type.daily',
						\Tx\CzSimpleCal\Domain\Model\Enumeration\RecurranceType::DAILY
					),
					array(
						'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.recurrance_type.weekly',
						\Tx\CzSimpleCal\Domain\Model\Enumeration\RecurranceType::WEEKLY
					),
					array(
						'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.recurrance_type.monthly',
						\Tx\CzSimpleCal\Domain\Model\Enumeration\RecurranceType::MONTHLY
					),
					array(
						'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.recurrance_type.yearly',
						\Tx\CzSimpleCal\Domain\Model\Enumeration\RecurranceType::YEARLY
					),
				),
			)
		),
		'recurrance_subtype' => array(
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'exclude' => 1,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.recurrance_subtype',
			'displayCond' => 'FIELD:recurrance_type:!IN:0,,none,daily',
			'config' => array(
				'type' => 'select',
				'itemsProcFunc' => 'Tx\\CzSimpleCal\\Utility\\EventConfig->getRecurranceSubtype'
			)
		),
		'recurrance_until' => array(
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'exclude' => 1,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.recurrance_until',
			'config' => array(
				'type' => 'input',
				'size' => 12,
				'max' => 20,
				'eval' => 'null,date',
				'default' => NULL,
			)
		),
		'location_inline' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.location',
			'config' => Array(
				'type' => 'inline',
				'foreign_table' => 'tx_czsimplecal_domain_model_address',
				'foreign_field' => 'event_uid',
				'foreign_match_fields' => array(
					'event_field' => 'location_inline'
				),
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
				'appearance' => array(
					'showPossibleLocalizationRecords' => TRUE,
					'showRemovedLocalizationRecords' => TRUE,
					'showAllLocalizationLink' => TRUE,
					'showSynchronizationLink' => TRUE,
				),
			)
		),
		'location' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.location',
			'config' => Array(
				'type' => 'group',
				'internal_type' => 'db',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
				'allowed' => 'tx_czsimplecal_domain_model_address',
			)
		),
		'location_country' => array(
			'displayCond' => 'EXT:static_info_tables:LOADED:TRUE',
			'exclude' => 1,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.location_country',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'itemsProcFunc' => 'tx_staticinfotables_div->selectItemsTCA',
				'itemsProcFunc_config' => array(
					'table' => 'static_countries',
					'indexField' => 'cn_iso_3',
					'prependHotlist' => 1,
				),
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'organizer_inline' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.organizer',
			'config' => array(
				'type' => 'inline',
				'foreign_table' => 'tx_czsimplecal_domain_model_address',
				'foreign_field' => 'event_uid',
				'foreign_match_fields' => array(
					'event_field' => 'organizer_inline'
				),
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
				'appearance' => array(
					'showPossibleLocalizationRecords' => TRUE,
					'showRemovedLocalizationRecords' => TRUE,
					'showAllLocalizationLink' => TRUE,
					'showSynchronizationLink' => TRUE,
				),
			)
		),
		'organizer' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.organizer',
			'config' => array(
				'type' => 'group',
				'internal_type' => 'db',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
				'allowed' => 'tx_czsimplecal_domain_model_address',
			)
		),
		'organizer_country' => array(
			'displayCond' => 'EXT:static_info_tables:LOADED:TRUE',
			'exclude' => 1,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.organizer_country',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'itemsProcFunc' => 'tx_staticinfotables_div->selectItemsTCA',
				'itemsProcFunc_config' => array(
					'table' => 'static_countries',
					'indexField' => 'cn_iso_3',
					'prependHotlist' => 1,
				),
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'categories' => array(
			'l10n_mode' => 'exclude',
			'exclude' => 0,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.categories',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'tx_czsimplecal_domain_model_category',
				'MM' => 'tx_czsimplecal_event_category_mm',
				'size' => 10,
				'maxSize' => 20,
				'maxitems' => 9999
			)
		),
		'show_page_instead' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.show_page_instead',
			'config' => array(
				'eval' => 'trim',
				'max' => 256,
				'size' => 25,
				'softref' => 'typolink',
				'type' => 'input',

				'wizards' => array(
					'_PADDING' => 2,
					'link' => array(
						'icon' => 'link_popup.gif',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1',
						'script' => 'browse_links.php?mode=wizard',
						'title' => 'LLL:EXT:cms/locallang_ttc.xml:header_link_formlabel',
						'type' => 'popup',
					),
				),
			)
		),
		'exceptions' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.exceptions',
			'config' => array(
				'type' => 'inline',
				'foreign_table' => 'tx_czsimplecal_domain_model_exception',
				'foreign_field' => 'parent_uid',
				'foreign_table_field' => 'parent_table',
				'foreign_match_fields' => array(
					'parent_field' => 'exceptions',
				),
				'appearance' => array(
					'showPossibleLocalizationRecords' => TRUE,
					'showRemovedLocalizationRecords' => TRUE,
					'showAllLocalizationLink' => TRUE,
					'showSynchronizationLink' => TRUE,
				),
			),
		),
		'exception_groups' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.exception_groups',
			'config' => array(
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'tx_czsimplecal_domain_model_exceptiongroup',
				'MM' => 'tx_czsimplecal_event_exceptiongroup_mm',
				// foreign_table is required for Extbase.
				'foreign_table' => 'tx_czsimplecal_domain_model_exceptiongroup',
				'maxitems' => 99999,
				'size' => 5,
				'autoSizeMax' => 20
			)
		),
		'twitter_hashtags' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.twitter_hashtags',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
				'eval' => 'trim'
			)
		),
		'flickr_tags' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.flickr_tags',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
				'eval' => 'trim'
			)
		),
		'status' => array(
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'exclude' => 1,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.status',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.status.tentative',
						'TENTATIVE'
					),
					array(
						'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.status.confirmed',
						'CONFIRMED'
					),
					array(
						'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.status.cancelled',
						'CANCELLED'
					),
				),
				'default' => 'CONFIRMED',
			),
		),
		'slug' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.slug',
			'config' => array(
				'type' => 'none', // just show the value - don't make it editable
			)
		),
		'last_indexed' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.last_indexed',
			'config' => array(
				'type' => 'input',
				'size' => 12,
				'max' => 20,
				'eval' => 'date',
			)
		),
		'cruser_fe' => array(
			'exclude' => 1,
			'config' => array(
				'type' => 'none',
			),
		),
		'crgroup_fe' => array(
			'exclude' => 1,
			'config' => array(
				'type' => 'none',
			),
		),
	),
);