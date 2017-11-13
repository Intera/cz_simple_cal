<?php

namespace Tx\CzSimpleCal\Recurrance\Type;

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
 * weekly recurrance
 */
class Yearly extends Base
{
    const SUBTYPE_AUTO = 'auto';

    const SUBTYPE_RELATIVE_TO_EASTER = '';

    /**
     * get the configured subtypes of this recurrance
     *
     * @return array
     */
    public function getSubtypes()
    {
        return self::addLL(
            [
                static::SUBTYPE_AUTO,
                static::SUBTYPE_RELATIVE_TO_EASTER,
            ]
        );
    }

    /**
     * special method to build events taking place relative to easter
     *
     * @param CzSimpleCalDateTime $start
     * @param CzSimpleCalDateTime $end
     * @param CzSimpleCalDateTime $until
     * @throws \RuntimeException
     * @see http://de.php.net/manual/en/function.easter-days.php
     */
    protected function buildEaster($start, $end, $until)
    {
        if (!function_exists('easter_days') || !function_exists('easter_date')) {
            throw new \RuntimeException(
                'The function easter_days() or easter_date() is not available in your PHP installation.
				The binaries were probably not compiled using --enable-calendar. Contact your
				server administrator to fix this.'
            );
        }

        /**
         * calculate the day offset
         */

        /**
         * the day of the year of the date given as start
         * 0 is the 1st January
         *
         * @var integer
         */
        $dayOfYear = date('z', $start->getTimestamp());
        $year = date('Y', $start->getTimestamp());

        $dayOfYearEaster = $this->getDayOfYearForEasterSunday($year);

        /**
         * a string for DateTime->modify() to jump from easter sunday
         * to the desired date
         * null if no jumping required
         *
         * @var string|null
         */
        $diffDaysStart = $dayOfYear - $dayOfYearEaster;
        $diffDaysStart = sprintf('%+d days', $diffDaysStart);

        /**
         * a string for DateTime->modify() to jump from easter sunday
         * to the desired date
         * null if no jumping required
         *
         * @var string|null
         */
        $diffDaysEnd = date('z', $end->getTimestamp()) - $dayOfYearEaster;
        $diffDaysEnd = sprintf('%+d days', $diffDaysEnd);

        while ($until >= $start) {
            $this->timeline->add(
                [
                    'start' => $start->getTimestamp(),
                    'end' => $end->getTimestamp(),
                ],
                $this->event
            );

            // Calculate dates for the next year
            $year = $year + 1;
            /**
             * timestamp for easter sunday of a given year
             *
             * @var integer
             */
            $easter = easter_date($year);

            $start->setDate($year, date('n', $easter), date('j', $easter));
            if (!is_null($diffDaysStart)) {
                $start->modify($diffDaysStart);
            }

            $end->setDate($year, date('n', $easter), date('j', $easter));
            if (!is_null($diffDaysEnd)) {
                $end->modify($diffDaysEnd);
            }
        }
    }

    /**
     * the main method building the recurrance
     *
     * @return void
     */
    protected function doBuild()
    {
        $start = clone $this->event->getDateTimeObjectStart();
        $end = clone $this->event->getDateTimeObjectEnd();
        $until = $this->event->getDateTimeObjectRecurranceUntil();

        $interval = $this->event->getRecurranceSubtype();
        if ($interval === 'relativetoeaster') {
            $this->buildEaster($start, $end, $until);
            return;
        } else {
            $step = '+1 year';
        }

        while ($until >= $start) {
            $this->timeline->add(
                [
                    'start' => $start->getTimestamp(),
                    'end' => $end->getTimestamp(),
                ],
                $this->event
            );

            $start->modify($step);
            $end->modify($step);
        }
    }

    /**
     * get the day of the year of easter sunday for a given year
     *
     * starts with 0, so 0 is the 1st of January
     *
     * @param integer $year
     * @return integer
     */
    protected function getDayOfYearForEasterSunday($year = null)
    {
        /**
         * the day of the year of the equinox in March
         *
         * You don't know what it is? Well, its basically the 21st of March. :)
         *
         * @var integer
         * @see http://en.wikipedia.org/wiki/Equinox
         */
        $equinox = intval(date('z', mktime(0, 0, 0, 3, 21, $year)));
        return $equinox + easter_days($year);
    }
}
