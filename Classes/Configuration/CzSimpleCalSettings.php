 
<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Configuration;

/*                                                                        *
 * This script belongs to the TYPO3 extension "childcare".                *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class CzSimpleCalSettings implements SingletonInterface
{
    /**
     * @var array
     */
    protected $settings;

    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->settings = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
        );
    }

    public function numberOfItemsPerPage(): int
    {
        $itemsPerPage = $this->settings['itemsPerPage'] ?? '20';
        return (int)$itemsPerPage;
    }
}
