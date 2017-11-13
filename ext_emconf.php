<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Simple calendar using Extbase',
    'description' => 'A simple calendar.',
    'category' => 'plugin',
    'state' => 'alpha',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'author' => 'Christian Zenker',
    'author_email' => 'christian.zenker@599media.de',
    'author_company' => '599media GmbH',
    'version' => '1.0.0',
    '_md5_values_when_last_written' => '',
    'constraints' => [
        'depends' => ['typo3' => '6.2.3-7.6.99'],
        'conflicts' => [],
        'suggests' => [
            'scheduler' => '',
            'tt_address' => '',
        ],
    ],
];
