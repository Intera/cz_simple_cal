<?php
/** @noinspection PhpMissingStrictTypesDeclarationInspection */

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
    'version' => '9.0.0-dev',
    'constraints' => [
        'depends' => ['typo3' => '9.5.0-9.5.99'],
        'conflicts' => [],
        'suggests' => [
            'scheduler' => '',
            'tt_address' => '',
        ],
    ],
];
