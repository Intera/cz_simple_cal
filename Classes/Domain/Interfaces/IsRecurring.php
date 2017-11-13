<?php

namespace Tx\CzSimpleCal\Domain\Interfaces;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Christian Zenker <christian.zenker@599media.de>, 599media GmbH
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

use Tx\CzSimpleCal\Utility\DateTime as CzSimpleCalDateTime;

/**
 * This interface means this domain model could be recurring.
 */
interface IsRecurring extends HasTimespan
{
    /**
     * @return CzSimpleCalDateTime
     */
    public function getDateTimeObjectRecurranceUntil();

    /**
     * @return string
     */
    public function getRecurranceSubtype();

    /**
     * @return string
     */
    public function getRecurranceType();

    /**
     * @return integer
     */
    public function getRecurranceUntil();

    /**
     * @param string $recurranceSubtype
     * @return void
     */
    public function setRecurranceSubtype($recurranceSubtype);

    /**
     * @param string $recurranceType
     * @return void
     */
    public function setRecurranceType($recurranceType);

    /**
     * @param CzSimpleCalDateTime $recurranceUntil
     * @return void
     */
    public function setRecurranceUntil($recurranceUntil);
}
