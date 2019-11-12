<?php
declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$lllPrefix = 'LLL:'
    . 'EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tx_czsimplecal_domain_model_event.';

if (ExtensionManagementUtility::isLoaded('static_info_tables')) {
    $columns = [
        'event_languages' => [
            'exclude' => 1,
            'label' => $lllPrefix . 'event_languages',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'enableMultiSelectFilterTextfield' => true,
                'foreign_table' => 'static_languages',
                'foreign_table_where' => 'ORDER BY static_languages.lg_name_en',
                'size' => 5,
            ],
        ],
    ];

    ExtensionManagementUtility::addTCAcolumns('tx_czsimplecal_domain_model_event', $columns);
}
