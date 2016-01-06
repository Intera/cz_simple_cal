<?php
return array(
	'ctrl' => array(
		'title' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_address',
		'label' => 'name',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l18n_parent',
		'transOrigDiffSourceField' => 'l18n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden'
		),
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('cz_simple_cal') . 'Resources/Public/Icons/tx_czsimplecal_domain_model_address.gif'
	),
	'interface' => array(
		'showRecordFieldList' => 'name,address,zip,city,country'
	),
	'types' => array(
		'1' => array('showitem' => 'name,address,zip,city,country,homepage')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	),
	'columns' => array(
		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.language',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.php:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.php:LGL.default_value', 0)
				)
			)
		),
		'l18n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_czsimplecal_domain_model_category',
				'foreign_table_where' => 'AND tx_czsimplecal_domain_model_category.uid=###REC_FIELD_l18n_parent### AND tx_czsimplecal_domain_model_category.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough')
		),
		't3ver_label' => array(
			'displayCond' => 'FIELD:t3ver_label:REQ:true',
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.versionLabel',
			'config' => array(
				'type' => 'none',
				'cols' => 27,
			)
		),
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check',
			)
		),
		'homepage' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_address.homepage',
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
		'name' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_address.name',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
				'eval' => 'trim,required',
			)
		),
		'address' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_address.address',
			'config' => array(
				'type' => 'text',
				'cols' => 20,
				'rows' => 4,
				'eval' => 'trim',
			)
		),
		'zip' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_address.zip',
			'config' => array(
				'type' => 'input',
				'size' => 7,
				'max' => 12,
				'eval' => 'trim',
			)
		),
		'city' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_address.city',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
				'eval' => 'trim',
			)
		),
		'country' => array(
			'displayCond' => 'EXT:static_info_tables:LOADED:TRUE',
			'exclude' => 1,
			'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_address.country',
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
	),
);