<?php
namespace Int\CzSimpleCal\Utility;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "cz_simple_cal".            *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Int\CzSimpleCal\Domain\Model\Enumeration\EventTimeType;

/**
 * This utility determines the event time type.
 */
class EventTimeUtility implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * Returns the event time as a plaing string without HTML tags.
	 *
	 * @param \Tx\CzSimpleCal\Domain\Model\EventIndex $eventIndex
	 * @return string
	 */
	public function getPlainEventTimeForEventIndexEntry($eventIndex) {

		$eventTimeType = $this->getEventTimeType($eventIndex->getEvent());

		switch ($eventTimeType) {
			case EventTimeType::ALL_DATES:
				$eventTime = strftime('%x - ', $eventIndex->getStart()) . strftime('%x', $eventIndex->getEnd());
				break;
			case EventTimeType::START_DATE:
				$eventTime = strftime('%x', $eventIndex->getStart());
				break;
			case EventTimeType::START_DATE_TIME:
				$eventTime = strftime('%x %H:%M', $eventIndex->getStart());
				break;
			case EventTimeType::START_DATE_TIME_AND_END_TIME:
				$eventTime = strftime('%x %H:%M - ', $eventIndex->getStart()) . strftime('%H:%M', $eventIndex->getEnd());
				break;
			// Default to display all date / times
			default:
				$eventTime = strftime('%x %H:%M - ', $eventIndex->getStart()) . strftime('%x %H:%M', $eventIndex->getEnd());
				break;
		}

		return $eventTime;
	}

	/**
	 * Returns the event time type for the given Event.
	 *
	 * @param \Tx\CzSimpleCal\Domain\Model\Event $event
	 * @return string
	 */
	public function getEventTimeType($event) {
		// Default is all date / times
		$result = EventTimeType::ALL_DATE_TIMES;

		$startDay = $event->getStartDay();
		$startTime = $event->getStartTime();

		$endDay = $event->getEndDay();
		$endTime = $event->getEndTime();

		$displayEndDay = TRUE;

		if (is_null($endDay) || $startDay === $endDay) {
			$displayEndDay = FALSE;
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
}