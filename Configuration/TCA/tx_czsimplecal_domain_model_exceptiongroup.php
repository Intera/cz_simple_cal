<?php
return array(
	'ctrl' => array(
		'title' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_exceptiongroup',
		'label' => 'title',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden'
		),
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('cz_simple_cal') . 'Resources/Public/Icons/tx_czsimplecal_domain_model_exceptiongroup.gif'
	),
	'interface' => array(
		'showRecordFieldList' => 'title,exceptions'
	),
	'types' => array(
		'1' => array('showitem' => 'title,exceptions')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	),
	'columns' => array(
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check'
			)
		),
		'title' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_exceptiongroup.title',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			)
		),
		'exceptions' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_exceptiongroup.exceptions',
			'config' => array(
				'type' => 'inline',
				'foreign_table' => 'tx_czsimplecal_domain_model_exception',
				'foreign_field' => 'parent_uid',
				'foreign_table_field' => 'parent_table',
				'foreign_match_fields' => array(
					'parent_field' => 'exceptions',
				),
			)
		),
	),
);