<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */
/** @noinspection PhpMissingStrictTypesDeclarationInspection */

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// TCA config
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_czsimplecal_domain_model_event',
    'EXT:cz_simple_cal/Resources/Private/Language/locallang_csh_tx_czsimplecal_domain_model_event.xlf'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_czsimplecal_domain_model_event');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_czsimplecal_domain_model_exception',
    'EXT:cz_simple_cal/Resources/Private/Language/locallang_csh_tx_czsimplecal_domain_model_exception.xlf'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_czsimplecal_domain_model_exception');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_czsimplecal_domain_model_exceptiongroup',
    'EXT:cz_simple_cal/Resources/Private/Language/locallang_csh_tx_czsimplecal_domain_model_exceptiongroup.xlf'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
    'tx_czsimplecal_domain_model_exceptiongroup'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_czsimplecal_domain_model_category',
    'EXT:cz_simple_cal/Resources/Private/Language/locallang_csh_tx_czsimplecal_domain_model_category.xlf'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_czsimplecal_domain_model_category');
