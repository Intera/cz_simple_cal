<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Utility;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration as TYPO3ExtensionConfiguration;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ExtensionConfiguration implements SingletonInterface
{
    public function isUsingSingleCategory(): bool
    {
        return (bool)GeneralUtility::makeInstance(TYPO3ExtensionConfiguration::class)
            ->get('cz_simple_cal', 'useSingleCategory');
    }
}
