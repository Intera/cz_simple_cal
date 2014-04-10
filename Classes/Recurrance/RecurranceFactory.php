<?php
namespace Tx\CzSimpleCal\Recurrance;

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

use Tx\CzSimpleCal\Domain\Model\Event as EventModel;
use Tx\CzSimpleCal\Domain\Model\Exception as ExceptionModel;
use Tx\CzSimpleCal\Recurrance\Timeline\Event as TimelineEvent;
use Tx\CzSimpleCal\Recurrance\Timeline\Exception as TimelineException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Tx\CzSimpleCal\Recurrance\Type\Base as RecurranceTypeBase;

/**
 * manages the build of all recurrant events
 */
class RecurranceFactory {

	/**
	 * the event to build the recurrance for
	 *
	 * @var EventModel
	 */
	protected $event = null;

	/**
	 * build the recurrance for an event
	 *
	 * @param \Tx\CzSimpleCal\Domain\Model\BaseEvent $event
	 * @return \Tx\CzSimpleCal\Domain\Model\Event
	 * @throws \InvalidArgumentException
	 */
	public function buildRecurranceForEvent($event) {
		if(!$event instanceof \Tx\CzSimpleCal\Domain\Model\BaseEvent) {
			// no type hinting to make it more reusable
			throw new \InvalidArgumentException(sprintf('$event must be of class \Tx\CzSimpleCal\Domain\Model\BaseEvent in %s::%s', get_class($this), __METHOD__));
		}

		$this->event = $event;

		/**
		 * a class holding all possible events ordered by their starttime ascending
		 *
		 * @var TimelineEvent
		 */
		$events = $this->buildEventTimeline();

		/**
		 * a class holding all exceptions
		 */
		$exceptions = $this->buildExceptionTimeline();

		return $this->dropExceptionalEvents($events, $exceptions);
	}

	/**
	 * build the recurrance for all events paying no attention to exceptions
	 *
	 * @return TimelineEvent
	 * @throws \RuntimeException
	 * @throws \BadMethodCallException
	 */
	protected function buildEventTimeline() {

		$type = $this->event->getRecurranceType();
		if(empty($type)) {
			throw new \RuntimeException('The recurrance_type should not be empty.');
		}

		$className = 'Tx\\CzSimpleCal\\Recurrance\\Type\\' . GeneralUtility::underscoredToUpperCamelCase($type);

		if(!class_exists($className)) {
			throw new \BadMethodCallException(sprintf('The class %s does not exist for creating recurring events.', $className));
		}

		$class = GeneralUtility::makeInstance($className);

		if(!$class instanceof RecurranceTypeBase) {
			throw new \BadMethodCallException(sprintf('The class %s does not implement \\Tx\\CzSimpleCal\\Recurrance\\Type\\Base.', get_class($class)));
		}

		$eventTimeline = new TimelineEvent();
		$eventTimeline->setEvent($this->event);

		return $class->build($this->event, $eventTimeline);
	}

	/**
	 * build the exception timeline
	 *
	 * @return TimelineException
	 * @throws \RuntimeException
	 * @throws \BadMethodCallException
	 */
	protected function buildExceptionTimeline() {
		$exceptionTimeline = new TimelineException();

		/** @var ExceptionModel $exception */
		foreach($this->event->getExceptions() as $exception) {

			$type = $exception->getRecurranceType();
			if(empty($type)) {
				throw new \RuntimeException('The recurrance_type should not be empty.');
			}

			$className = 'Tx\\CzSimpleCal\\Recurrance\\Type\\' . GeneralUtility::underscoredToUpperCamelCase($type);

			if(!class_exists($className)) {
				throw new \BadMethodCallException(sprintf('The class %s does not exist for creating recurring events.', $className));
			}

			$class = GeneralUtility::makeInstance($className);

			if(!$class instanceof RecurranceTypeBase) {
				throw new \BadMethodCallException(sprintf('The class %s does not implement \\Tx\\CzSimpleCal\\Recurrance\\Type\\Base.', get_class($class)));
			}

			$exceptionTimeline = $class->build($exception, $exceptionTimeline);
		}
		return $exceptionTimeline;
	}

	/**
	 * drop all events that are blocked by an exception
	 *
	 * some words on how it works:
	 *
	 * Basically the idea here is to check every event if it overlaps an exception.
	 *
	 * To make this algorithm a bit more efficant, these prerequisits are met:
	 *  - the events are ordered by their start-date (no duplicate start dates),
	 *  - the exceptions by their start-date (no duplicate start dates)
	 *
	 * So if we find, that the end-date of an exception is before the current start-date
	 * it is before the start-date of ALL remaining events and we'll just drop it.
	 *
	 *
	 * @param TimelineEvent $events
	 * @param TimelineException $exceptions
	 * @return TimelineEvent
	 */
	protected function dropExceptionalEvents($events, $exceptions) {

		foreach($events as $eventKey=>$event) {

			if(!$exceptions->hasData()) {
				break;
			}

//			$exceptions->rewind();
			foreach($exceptions as $exceptionKey=>$exception) {

				if($exception['end'] <= $eventKey /*eventKey = $event['start']*/) {
					//if: end of exception is before start of event -> delete it as it won't affect any more of the events
					$exceptions->unsetCurrent();
				} elseif($event['end'] < $exceptionKey /*exceptionKey = $exception['start']*/ ||
						($event['end'] == $exceptionKey && $event['start'] != $event['end'] )) {
					//if: end of event is before start of exception or
					//    end of event matches start of exception and the event is not zero length
					//    -> none of the following exception will affect this event
					break;
				} else {
					// else: match -> delete this event
					$events->unsetCurrent();
					break;
				}
			}
		}
		return $events;

	}

}