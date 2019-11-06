<?php
declare(strict_types=1);

use SJBR\StaticInfoTables\Hook\Backend\Form\FormDataProvider\TcaSelectItemsProcessor;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

if (ExtensionManagementUtility::isLoaded('static_info_tables')) {
    $columns = [
        'country' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:tx_czsimplecal_domain_model_address.country',
            'config' => [
                'type' => 'select',
                'items' => [['', 0]],
                'foreign_table' => 'static_countries',
                'foreign_table_where' => 'ORDER BY static_countries.cn_short_en',
                'itemsProcFunc' => TcaSelectItemsProcessor::class . '->translateCountriesSelector',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
    ];

    ExtensionManagementUtility::addTCAcolumns('tx_czsimplecal_domain_model_address', $columns);
}
