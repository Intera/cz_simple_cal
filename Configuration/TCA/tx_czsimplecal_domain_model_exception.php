<?php
$languagePrefix = 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:';
$languagePrefixColumn = $languagePrefix . 'tx_czsimplecal_domain_model_exception.';
$commonFields = 'type, title, start_day, start_time, end_day, end_time, --palette--;;system,
	--div--;' . $languagePrefixColumn . 'tab_recurrance,
	recurrance_type, recurrance_subtype, recurrance_until';
return array(
	'ctrl' => array(
		'title' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_exception',
		'label' => 'title',
		'delete' => 'deleted',
		'hideTable' => TRUE,
		'type' => 'type',
		'enablecolumns' => array(
			'disabled' => 'hidden'
		),
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('cz_simple_cal') . 'Resources/Public/Icons/tx_czsimplecal_domain_model_exception.gif',
		'dividers2tabs' => 1,
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
	),
	'interface' => array(
		'showRecordFieldList' => 'type,title,start_day,start_time,end_day,end_time,recurrance_type,recurrance_subtype,recurrance_until,status,teaser'
	),
	'types' => array(
		'0' => array('showitem' => 'type'),
		\Tx\CzSimpleCal\Domain\Model\Enumeration\ExceptionType::HIDE_EVENT => array('showitem' => $commonFields),
		\Tx\CzSimpleCal\Domain\Model\Enumeration\ExceptionType::UPDATE_EVENT => array('showitem' => $commonFields . '
			,--div--;' . $languagePrefixColumn . 'tab_update_event_properties,
			status, teaser
		'),
	),
	'palettes' => array(
		'system' => array(
			'showitem' => 'hidden, sys_language_uid, l10n_parent',
			'isHiddenPalette' => TRUE
		)
	),
	'columns' => array(
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
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
		'title' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_exception.title',
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
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough'
			),
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 0,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0)
				),
				'foreign_table' => 'tx_czsimplecal_domain_model_exception',
				'foreign_table_where' => 'AND tx_czsimplecal_domain_model_exception.uid=###REC_FIELD_l10n_parent### AND tx_czsimplecal_domain_model_exception.sys_language_uid IN (-1,0)'
			)
		),
		'timezone' => array(
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
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
						'none'
					),
					array(
						'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.recurrance_type.daily',
						'daily'
					),
					array(
						'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.recurrance_type.weekly',
						'weekly'
					),
					array(
						'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.recurrance_type.monthly',
						'monthly'
					),
					array(
						'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_event.recurrance_type.yearly',
						'yearly'
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
		'status' => array(
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'exclude' => 1,
			'label' => $languagePrefixColumn . 'status',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						$languagePrefixColumn . 'status.I.undefined',
						\Tx\CzSimpleCal\Domain\Model\Enumeration\EventStatus::UNDEFINED
					),
					array(
						$languagePrefixColumn . 'status.I.tentative',
						\Tx\CzSimpleCal\Domain\Model\Enumeration\EventStatus::TENTATIVE
					),
					array(
						$languagePrefixColumn . 'status.I.confirmed',
						\Tx\CzSimpleCal\Domain\Model\Enumeration\EventStatus::CONFIRMED
					),
					array(
						$languagePrefixColumn . 'status.I.cancelled',
						\Tx\CzSimpleCal\Domain\Model\Enumeration\EventStatus::CANCELLED
					),
				),
				'default' => \Tx\CzSimpleCal\Domain\Model\Enumeration\EventStatus::CONFIRMED,
			),
		),
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
		'teaser' => array(
			'exclude' => 1,
			'label' => $languagePrefixColumn . 'teaser',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 6,
				'eval' => 'null,trim',
				'default' => NULL
			)
		),
		'type' => array(
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'exclude' => 0,
			'label' => $languagePrefixColumn . 'type',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						$languagePrefixColumn . 'type.I.hide_event',
						\Tx\CzSimpleCal\Domain\Model\Enumeration\ExceptionType::HIDE_EVENT
					),
					array(
						$languagePrefixColumn . 'type.I.update_event',
						\Tx\CzSimpleCal\Domain\Model\Enumeration\ExceptionType::UPDATE_EVENT
					),
				),
				'default' => \Tx\CzSimpleCal\Domain\Model\Enumeration\ExceptionType::HIDE_EVENT,
			),
		),
	),
);