<?php

namespace Tx\CzSimpleCal\Domain\Model;

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

use Tx\CzSimpleCal\Domain\Interfaces\IsRecurring;

/**
 * A base version for an event with start, end and possible recurrance
 */
abstract class BaseEvent extends Base implements IsRecurring
{
    /**
     * @var \Tx\CzSimpleCal\Utility\DateTime
     */
    protected $endDateTime = null;

    /**
     * the day this event ends (leave blank for an event on one day)
     *
     * @var integer
     */
    protected $endDay;

    /**
     * the time this event ends
     *
     * @var integer
     */
    protected $endTime;

    /**
     * the subtype of recurrance
     *
     * @var string
     */
    protected $recurranceSubtype;

    /**
     * the type of recurrance
     *
     * @var string
     * @validate StringLength(maximum=30)
     */
    protected $recurranceType = 'none';

    /**
     * recurrance until this date
     *
     * @var integer
     * @validate StringLength(maximum=30)
     */
    protected $recurranceUntil;

    /**
     * @var \Tx\CzSimpleCal\Utility\DateTime
     */
    protected $recurranceUntilDateTime = null;

    /**
     * @var \Tx\CzSimpleCal\Utility\DateTime
     */
    protected $startDateTime = null;

    /**
     * the day that event starts
     *
     * @var integer
     */
    protected $startDay;

    /**
     * the time this event starts (leave blank for an allday event)
     *
     * @var integer
     */
    protected $startTime;

    /**
     * the timezone of the user who created that event
     *
     * @var string
     * @validate StringLength(maximum=20)
     */
    protected $timezone;

    /**
     * get a DateTime object of the end
     *
     * @return \Tx\CzSimpleCal\Utility\DateTime
     */
    public function getDateTimeObjectEnd()
    {
        if (is_null($this->endDateTime)) {
            $this->createDateTimeObjects();
        }
        return $this->endDateTime;
    }

    /**
     * get a DateTime object of the recurranceUntil feature
     *
     * @return \DateTime
     */
    public function getDateTimeObjectRecurranceUntil()
    {
        if (is_null($this->recurranceUntilDateTime)) {
            $this->recurranceUntilDateTime = new \Tx\CzSimpleCal\Utility\DateTime(
                $this->recurranceUntil > 0 ?
                    '@' . $this->recurranceUntil :
                    \Tx\CzSimpleCal\Utility\Config::get('recurrenceEnd')
            );
            $this->recurranceUntilDateTime->setTimezone(new \DateTimeZone($this->timezone));
            $this->recurranceUntilDateTime->setTime(23, 59, 59);
        }

        return $this->recurranceUntilDateTime;
    }

    /**
     * get a DateTime object of the start
     *
     * @return \Tx\CzSimpleCal\Utility\DateTime
     */
    public function getDateTimeObjectStart()
    {
        if (is_null($this->startDateTime)) {
            $this->createDateTimeObjects();
        }
        return $this->startDateTime;
    }

    /**
     * Getter for endDay
     *
     * @return integer the day this event ends (leave blank for an event on one day)
     */
    public function getEndDay()
    {
        return is_numeric($this->endDay) ?
            (strftime('%Y-%m-%d', $this->endDay)) :
            $this->endDay;
    }

    /**
     * Getter for endTime
     *
     * @return integer the time this event ends
     */
    public function getEndTime()
    {
        return is_numeric($this->endTime) ?
            (sprintf('%02d:%02d', floor($this->endTime / 3600), floor($this->endTime % 3600 / 60))) :
            $this->endTime;
    }

    /**
     * get the recurrance subtype
     *
     * @return string
     */
    public function getRecurranceSubtype()
    {
        return $this->recurranceSubtype;
    }

    /**
     * get the recurrance type
     *
     * @return string
     */
    public function getRecurranceType()
    {
        return $this->recurranceType;
    }

    /**
     * get the recurrance until raw value
     *
     * @return integer
     */
    public function getRecurranceUntil()
    {
        return $this->recurranceUntil;
    }

    /**
     * Getter for startDay
     *
     * @return integer the day that event starts
     */
    public function getStartDay()
    {
        return is_numeric($this->startDay) ? strftime('%Y-%m-%d', $this->startDay) : $this->startDay;
    }

    /**
     * Getter for startTime
     *
     * @return integer the time this event starts (leave blank for an allday event)
     */
    public function getStartTime()
    {
        return is_numeric($this->startTime) ?
            (sprintf('%02d:%02d', floor($this->startTime / 3600), floor($this->startTime % 3600 / 60))) :
            $this->startTime;
    }

    /**
     * get the timezone of the user who created that event
     *
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Setter for endDay
     *
     * @param integer $endDay the day this event ends (leave blank for an event on one day)
     * @return void
     */
    public function setEndDay($endDay)
    {
        $this->endDay = (empty($endDay) && $endDay !== 0) || $endDay < 0 ?
            null :
            $endDay;
    }

    /**
     * Setter for endTime
     *
     * @param integer $endTime the time this event ends
     * @return void
     */
    public function setEndTime($endTime)
    {
        $this->endTime = is_null($endTime) ? null : (int)$endTime;
    }

    /**
     * set the recurrance subtype
     *
     * @param string $recurranceSubtype
     */
    public function setRecurranceSubtype($recurranceSubtype)
    {
        $this->recurranceSubtype = $recurranceSubtype;
    }

    /**
     * set the recurrance type
     *
     * @param string $recurranceType
     * @return void
     */
    public function setRecurranceType($recurranceType)
    {
        $this->recurranceType = $recurranceType;
    }

    /**
     * set the recurrance until value
     *
     * @param integer $recurranceUntil
     * @return void
     */
    public function setRecurranceUntil($recurranceUntil)
    {
        $this->recurranceUntil = $recurranceUntil;
    }

    /**
     * Setter for startDay
     *
     * @param integer $startDay the day that event starts
     * @return void
     */
    public function setStartDay($startDay)
    {
        $this->startDay = $startDay;
    }

    /**
     * Setter for startTime
     *
     * @param integer $startTime the time this event starts (leave blank for an allday event)
     * @return void
     */
    public function setStartTime($startTime)
    {
        $this->startTime = is_null($startTime) ? null : (int)$startTime;
    }

    /**
     * set the timezone of the user who created that event
     *
     * @param string $timezone
     * @return null
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * create the DateTimeObjects of start and end
     *
     * @return null
     */
    protected function createDateTimeObjects()
    {
        /*
         * little excursus on how TYPO3 treats dates and times before writing them to the database:
         *
         * - input eval date will store the timestamp of midnight of the day this event takes place in the
         *   default timezone of the server. So if the server stands in Greenwich 1-1-1970 will be stored as "0",
         *   but if your server is in Berlin it would be stored as "3600".
         * - input eval time will just store the seconds from midnight.
         *
         * so if we add both the date and the time, we will get a valid timestamp of the event
         */

        $start = $this->startDay + max(0, $this->startTime);

        if ($this->endTime === null) {
            if ($this->startTime === null) {
                // If: no start and no end time -> this is an allday event -> set end as end of the day
                $end = max($this->endDay, $this->startDay) + 86399;
            } else {
                // If: no end but a start time -> this is an event with default length
                $end = max($this->endDay, $this->startDay) + $this->startTime;
            }
        } else {
            // If: there is an endTime -> just a casual event
            $end = max($this->endDay, $this->startDay) + $this->endTime;
        }

        // Start time
        $this->startDateTime = new \Tx\CzSimpleCal\Utility\DateTime(
            '@' . $start // "@": let this be parsed as a unix timestamp
        );
        $this->startDateTime->setTimezone(new \DateTimeZone($this->timezone));

        // End time
        $this->endDateTime = new \Tx\CzSimpleCal\Utility\DateTime(
            '@' . $end
        );
        $this->endDateTime->setTimezone(new \DateTimeZone($this->timezone));
    }
}
