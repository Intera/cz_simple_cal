<?php

namespace Tx\CzSimpleCal\Hook;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Alexander Stehlik <astehlik.deleteme@intera.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Backend\Form\FormDataProviderInterface;

/**
 * This class provides methods for rendering additional input fields
 * for the form engine.
 */
class BackendFormEnhancements implements FormDataProviderInterface
{
    const LANGUAGE_PREFIX = 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:';

    /**
     * Add form data to result array
     *
     * @param array $result Initialized result array
     * @return array Result filled with more data
     */
    public function addData(array $result)
    {
        if ($result['tableName'] !== 'tx_czsimplecal_domain_model_event') {
            return $result;
        }

        if (!isset($result['databaseRow']['uid']) || (int)$result['databaseRow'] === 0) {
            return $result;
        }

        if ($result['databaseRow']['sys_language_uid'][0] > 0) {
            return $result;
        }

        $this->registerAdditionalEventFields($result['processedTca']);

        return $result;
    }

    protected function registerAdditionalEventFields(array &$eventTca)
    {
        $eventTca['columns']['regenerate_slug'] = [
            'label' => $this->getLanguagePrefixEventColumn() . 'regenerate_slug',
            'config' => ['type' => 'check'],
        ];
    }

    private function getLanguagePrefixEventColumn()
    {
        return static::LANGUAGE_PREFIX . 'tx_czsimplecal_domain_model_event.';
    }
}
