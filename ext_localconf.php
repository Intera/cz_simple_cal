<?php
/** @noinspection PhpMissingStrictTypesDeclarationInspection */
/** @noinspection PhpFullyQualifiedNameUsageInspection */

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

/** @uses \Tx\CzSimpleCal\Controller\EventIndexController::listAction() */
/** @uses \Tx\CzSimpleCal\Controller\EventIndexController::countEventsAction() */
/** @uses \Tx\CzSimpleCal\Controller\EventIndexController::showAction() */
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

/** @uses \Tx\CzSimpleCal\Controller\EventAdministrationController::listAction() */
/** @uses \Tx\CzSimpleCal\Controller\EventAdministrationController::newAction() */
/** @uses \Tx\CzSimpleCal\Controller\EventAdministrationController::createAction() */
/** @uses \Tx\CzSimpleCal\Controller\EventAdministrationController::editAction() */
/** @uses \Tx\CzSimpleCal\Controller\EventAdministrationController::updateAction() */
/** @uses \Tx\CzSimpleCal\Controller\EventAdministrationController::deleteAction() */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Tx.CzSimpleCal',
    'Pi2',
    ['EventAdministration' => 'list,new,create,edit,update,delete'],
    ['EventAdministration' => 'list,new,create,edit,update,delete']
);

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
    = Tx\CzSimpleCal\Hook\DatabaseRecordListHook::class;

// Hook into the post storing process to update the index of recurring events
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][]
    = Tx\CzSimpleCal\Hook\DataHandlerHook::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][]
    = Tx\CzSimpleCal\Hook\DataHandlerHook::class;

$formDataGroupConfig = &$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup'];

$formDataGroupConfig['tcaDatabaseRecord'][\Tx\CzSimpleCal\Hook\BackendFormEnhancements::class] = [
    'depends' => [\TYPO3\CMS\Backend\Form\FormDataProvider\InitializeProcessedTca::class],
    'before' => [\TYPO3\CMS\Backend\Form\FormDataProvider\TcaColumnsProcessFieldLabels::class],
];

unset($formDataGroupConfig);
