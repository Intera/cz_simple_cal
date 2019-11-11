<?php
declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::addStaticFile(
    'cz_simple_cal',
    'Configuration/TypoScript/main',
    'Simple calendar using Extbase'
);
ExtensionManagementUtility::addStaticFile(
    'cz_simple_cal',
    'Configuration/TypoScript/customaddress',
    'Use custom address extension'
);
ExtensionManagementUtility::addStaticFile(
    'cz_simple_cal',
    'Configuration/TypoScript/ics',
    'ICS configuration'
);
