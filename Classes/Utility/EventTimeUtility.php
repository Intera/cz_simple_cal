<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Utility;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "cz_simple_cal".            *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Tx\CzSimpleCal\Domain\Model\Enumeration\EventTimeType;
use Tx\CzSimpleCal\Domain\Model\Event;
use Tx\CzSimpleCal\Domain\Model\EventIndex;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * This utility determines the event time type.
 */
class EventTimeUtility implements SingletonInterface
{
    /**
     * Returns the event time type for the given Event.
     *
     * @param Event $event
     * @return string
     */
    public function getEventTimeType($event)
    {
        // Default is all date / times
        $result = EventTimeType::ALL_DATE_TIMES;

        $startDay = $event->getStartDay();
        $startTime = $event->getStartTime();

        $endDay = $event->getEndDay();
        $endTime = $event->getEndTime();

        $displayEndDay = true;

        if (is_null($endDay) || $startDay === $endDay) {
            $displayEndDay = false;
        }

        if (!$displayEndDay) {
            if (isset($startTime) && isset($endTime)) {
                if ($startTime !== $endTime) {
                    $result = EventTimeType::START_DATE_TIME_AND_END_TIME;
                } else {
                    $result = EventTimeType::START_DATE_TIME;
                }
            } elseif (isset($startTime) && !isset($endTime)) {
                $result = EventTimeType::START_DATE_TIME;
            } elseif (!isset($startTime) && !isset($endTime)) {
                $result = EventTimeType::START_DATE;
            }
        } else {
            if (!isset($startTime) && !isset($endTime)) {
                $result = EventTimeType::ALL_DATES;
            }
        }

        return $result;
    }

    /**
     * Returns the event time as a plaing string without HTML tags.
     *
     * @param EventIndex $eventIndex
     * @return string
     */
    public function getPlainEventTimeForEventIndexEntry($eventIndex): string
    {
        $eventTimeType = $this->getEventTimeType($eventIndex->getEvent());

        switch ($eventTimeType) {
            case EventTimeType::ALL_DATES:
                $eventTime = strftime('%x - ', $eventIndex->getStart())
                    . strftime('%x', $eventIndex->getEnd());
                break;
            case EventTimeType::START_DATE:
                $eventTime = strftime('%x', $eventIndex->getStart());
                break;
            case EventTimeType::START_DATE_TIME:
                $eventTime = strftime('%x %H:%M', $eventIndex->getStart());
                break;
            case EventTimeType::START_DATE_TIME_AND_END_TIME:
                $eventTime = strftime('%x %H:%M - ', $eventIndex->getStart())
                    . strftime('%H:%M', $eventIndex->getEnd());
                break;
            // Default to display all date / times
            default:
                $eventTime = strftime('%x %H:%M - ', $eventIndex->getStart())
                    . strftime('%x %H:%M', $eventIndex->getEnd());
                break;
        }

        return $eventTime;
    }
}
