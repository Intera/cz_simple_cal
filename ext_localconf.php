<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Tx.CzSimpleCal',
    'Pi1',
    [
        'EventIndex' => 'list,countEvents,show',
        'Event' => 'show',
        'Category' => 'show',
    ],
    []
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Tx.CzSimpleCal',
    'Pi2',
    ['EventAdministration' => 'list,new,create,edit,update,delete'],
    ['EventAdministration' => 'list,new,create,edit,update,delete']
);

$locallangModPrefix = 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_mod.xml:';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Tx\\CzSimpleCal\\Scheduler\\IndexTask'] = [
    'extension' => $_EXTKEY,
    'title' => $locallangModPrefix . 'tx_czsimplecal_scheduler_index.label',
    'description' => $locallangModPrefix . 'tx_czsimplecal_scheduler_index.description',
    'additionalFields' => 'Tx\\CzSimpleCal\\Scheduler\\IndexTask',
];

unset($locallangModPrefix);

// Add default pageTSConfig
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    file_get_contents(
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath(
            'cz_simple_cal'
        ) . 'Configuration/TSconfig/default.txt'
    )
);

// Register the hook that filters inline addresses from the record list.
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/class.db_list_extra.inc']['getTable'][]
    = 'Tx\\CzSimpleCal\\Hook\\DatabaseRecordListHook';

// Hook into the post storing process to update the index of recurring events
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][]
    = 'Tx\\CzSimpleCal\\Hook\\DataHandlerHook';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][]
    = 'Tx\\CzSimpleCal\\Hook\\DataHandlerHook';
