<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    $_EXTKEY,
    'Pi1',
    'Simple calendar using Extbase'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    $_EXTKEY,
    'Pi2',
    'Calendar event submission for users'
);

// Default typoscript
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $_EXTKEY,
    'Configuration/TypoScript/main',
    'Simple calendar using Extbase'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $_EXTKEY,
    'Configuration/TypoScript/customaddress',
    'Use custom address extension'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $_EXTKEY,
    'Configuration/TypoScript/ics',
    'ICS configuration'
);

// Init flexform for plugin
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['czsimplecal_pi1'] = 'pi_flexform';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['czsimplecal_pi1'] = 'layout,select_key';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'czsimplecal_pi1',
    'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform.xml'
);

// TCA config
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_czsimplecal_domain_model_event',
    'EXT:cz_simple_cal/Resources/Private/Language/locallang_csh_tx_czsimplecal_domain_model_event.xml'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_czsimplecal_domain_model_event');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_czsimplecal_domain_model_exception',
    'EXT:cz_simple_cal/Resources/Private/Language/locallang_csh_tx_czsimplecal_domain_model_exception.xml'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_czsimplecal_domain_model_exception');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_czsimplecal_domain_model_exceptiongroup',
    'EXT:cz_simple_cal/Resources/Private/Language/locallang_csh_tx_czsimplecal_domain_model_exceptiongroup.xml'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
    'tx_czsimplecal_domain_model_exceptiongroup'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_czsimplecal_domain_model_category',
    'EXT:cz_simple_cal/Resources/Private/Language/locallang_csh_tx_czsimplecal_domain_model_category.xml'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_czsimplecal_domain_model_category');
