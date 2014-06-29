<?php
// This TCA is needed to get the Domain Object Mapper of Extbase to work,
// but this table should not be displayed in the Frontent.
return array(
	'ctrl' => array(
		'title' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_eventindex',
		'label' => '',
		'hideTable' => TRUE,
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('cz_simple_cal') . 'Resources/Public/Icons/tx_czsimplecal_domain_model_event.gif'
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
		'teaser' => array(
			'exclude' => 0,
			'label' => '',
			'config' => array(
				'type' => 'none',
			)
		),
	),
);