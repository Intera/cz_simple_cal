<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Tx.CzSimpleCal',
	'Pi1',
	array(
		'EventIndex' => 'list,countEvents,show',
		'Event' => 'show',
		'Category' => 'show',
	),
	array()
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Tx.CzSimpleCal',
	'Pi2',
	array(
		'EventAdministration' => 'list,new,create,edit,update,delete',
	),
	array(
		'EventAdministration' => 'list,new,create,edit,update,delete',
	)
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Tx\\CzSimpleCal\\Scheduler\\IndexTask'] = array(
	'extension' => $_EXTKEY,
	'title' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_mod.xml:tx_czsimplecal_scheduler_index.label',
	'description' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_mod.xml:tx_czsimplecal_scheduler_index.description',
	'additionalFields' => 'Tx\\CzSimpleCal\\Scheduler\\IndexTask'
);

// add default pageTSConfig
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
	file_get_contents(
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('cz_simple_cal') . 'Configuration/TSconfig/default.txt'
	)
);

// Register the hook that filters inline addresses from the record list.
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/class.db_list_extra.inc']['getTable'][] = 'Tx\\CzSimpleCal\\Hook\\DatabaseRecordListHook';

// Hook into the post storing process to update the index of recurring events
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'Tx\\CzSimpleCal\\Hook\\DataHandlerHook';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = 'Tx\\CzSimpleCal\\Hook\\DataHandlerHook';