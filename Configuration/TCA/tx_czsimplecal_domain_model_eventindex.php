<?php
// This TCA is needed to get the Domain Object Mapper of Extbase to work,
// but this table should not be displayed in the Frontent.
return array(
	'ctrl' => array(
		'title' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_eventindex',
		'label' => '',
		'hideTable' => TRUE,
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('cz_simple_cal') . 'Resources/Public/Icons/tx_czsimplecal_domain_model_event.gif',
		'languageField' => 'sys_language_uid',
	),
	'interface' => array(
		'showRecordFieldList' => ''
	),
	'types' => array(
		'1' => array('showitem' => '')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	),
	'columns' => array(
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
		'start' => array(
			'exclude' => 0,
			'label' => '',
			'config' => array(
				'type' => 'input',
				'eval' => 'datetime,required'
			)
		),
		'end' => array(
			'exclude' => 0,
			'label' => '',
			'config' => array(
				'type' => 'input',
				'eval' => 'datetime,required'
			)
		),
		'event' => array(
			'exclude' => 0,
			'label' => '',
			'config' => array(
				'type' => 'passthrough',
				'foreign_table' => 'tx_czsimplecal_domain_model_event',
			)
		),
		'slug' => array(
			'exclude' => 0,
			'label' => '',
			'config' => array(
				'type' => 'none',
			)
		),
		'status' => array(
			'exclude' => 0,
			'label' => '',
			'config' => array(
				'type' => 'none',
			)
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
			'exclude' => 0,
			'label' => '',
			'config' => array(
				'type' => 'none',
			)
		),
	),
);