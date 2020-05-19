<?php
declare(strict_types=1);

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

$lllPrefixListType = 'LLL:' . 'EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xlf:tt_content.list_type.I.';

ExtensionUtility::registerPlugin(
    'cz_simple_cal',
    'Pi1',
    $lllPrefixListType . 'czsimplecal_pi1',
    'EXT:cz_simple_cal/Resources/Public/Icons/content_calendar.svg'
);

ExtensionUtility::registerPlugin(
    'cz_simple_cal',
    'Slider',
    $lllPrefixListType . 'czsimplecal_slider',
    'EXT:cz_simple_cal/Resources/Public/Icons/content_calendar_slider.svg'
);

ExtensionUtility::registerPlugin(
    'cz_simple_cal',
    'Pi2',
    'Calendar event submission for users',
    'EXT:cz_simple_cal/Resources/Public/Icons/content_calendar.svg'
);

$extensionConfig = GeneralUtility::makeInstance(ExtensionConfiguration::class);
$flexFormType = $extensionConfig->get('cz_simple_cal', 'flexFormType') ?: 'advanced';

// Init flexform for plugin
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['czsimplecal_pi1'] = 'pi_flexform';
ExtensionManagementUtility::addPiFlexFormValue(
    'czsimplecal_pi1',
    sprintf('FILE:EXT:cz_simple_cal/Configuration/FlexForms/flexform_%s.xml', $flexFormType)
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['czsimplecal_slider'] = 'pi_flexform';
ExtensionManagementUtility::addPiFlexFormValue(
    'czsimplecal_slider',
    sprintf('FILE:EXT:cz_simple_cal/Configuration/FlexForms/flexform_%s.xml', $flexFormType)
);
