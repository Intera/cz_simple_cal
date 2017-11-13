<?php

namespace Tx\CzSimpleCal\Hook;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Alexander Stehlik <astehlik.deleteme@intera.de>
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

/**
 * Hooks for the database record list. This hook is currently used
 * to hide the inline addresses in the record list.
 */
class DatabaseRecordListHook implements \TYPO3\CMS\Backend\RecordList\RecordListGetTableHookInterface
{
    /**
     * Filters inline records from the records list.
     *
     * @param string $table The current database table
     * @param integer $pageId The record's page ID
     * @param string $additionalWhereClause An additional WHERE clause
     * @param string $selectedFieldsList Comma separated list of selected fields
     * @param \TYPO3\CMS\Recordlist\RecordList\DatabaseRecordList $parentObject Parent localRecordList object
     * @return void
     */
    public function getDBlistQuery($table, $pageId, &$additionalWhereClause, &$selectedFieldsList, &$parentObject)
    {
        if ($table !== 'tx_czsimplecal_domain_model_address') {
            return;
        }

        $additionalWhereClause .= ' AND tx_czsimplecal_domain_model_address.event_uid = 0';
    }
}
