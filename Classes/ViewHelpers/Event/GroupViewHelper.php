<?php
namespace Tx\CzSimpleCal\ViewHelpers\Event;

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

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class GroupViewHelper extends AbstractViewHelper {

	/**
	 *
	 * @param array $events the events
	 * @param string $as the variable name the group events should be written to
	 * @param string $by one of the supported types
	 * @param string $orderBy
	 * @param string $order
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function render(
		/** @noinspection PhpUnusedParameterInspection */
		$events, $as, $by = 'day', $orderBy = '', $order=''
	) {
		$by = strtolower($by);
		if($by === 'day') {
			$events = $this->groupByTime($events, 'midnight');
		} elseif($by === 'month') {
			$events = $this->groupByTime($events, 'first day of this month midnight');
		} elseif($by === 'year') {
			$events = $this->groupByTime($events, 'january 1st midnight');
		} elseif($by === 'location') {
			$events = $this->groupByLocation($events);
		} elseif($by === 'organizer') {
			$events = $this->groupByOrganizer($events);
		} else {
			throw new \InvalidArgumentException(sprintf('%s can\'t group by "%s". Maybe a misspelling?', get_class($this), $by));
		}

		$this->templateVariableContainer->add($as, $events);
			$output = $this->renderChildren();
		$this->templateVariableContainer->remove($as);

		return $output;
	}

	/**
	 * do grouping by some time related constraint
	 *
	 * @param array $events
	 * @param string $string
	 * @return array
	 */
	protected function groupByTime($events, $string) {
		$result = array();
		/** @var \Tx\CzSimpleCal\Domain\Model\EventIndex $event */
		foreach($events as $event) {
			$key = \Tx\CzSimpleCal\Utility\StrToTime::strtotime($string, $event->getStart());
			if(!array_key_exists($key, $result)) {
				$result[$key] = array(
					'info' => $key,
					'events' => array(),
				);
			}

			$result[$key]['events'][] = $event;
		}
		return $result;
	}

	/**
	 * do grouping by location
	 *
	 * @param $events
	 */
	protected function groupByLocation($events) {
		$result = array();
		/** @var \Tx\CzSimpleCal\Domain\Model\Event $event */
		foreach($events as $event) {
			$locationKey = $event->getActiveLocation() ? $event->getActiveLocation()->getUid() : 0;
			if(!array_key_exists($locationKey, $result)) {
				$result[$locationKey] = array(
					'info' => $event->getActiveLocation() ? $event->getActiveLocation() : false,
					'events' => array(),
				);
			}

			$result[$locationKey]['events'][] = $event;
		}
		return $this->order($result);
	}

	/**
	 * do grouping by organizer
	 *
	 * @param $events
	 */
	protected function groupByOrganizer($events) {
		$result = array();
		/** @var \Tx\CzSimpleCal\Domain\Model\Event $event */
		foreach($events as $event) {
			$organizerKey = $event->getActiveOrganizer() ? $event->getActiveOrganizer()->getUid() : 0;
			if(!array_key_exists($organizerKey, $result)) {
				$result[$organizerKey] = array(
					'info' => $event->getActiveOrganizer() ? $event->getActiveOrganizer() : false,
					'events' => array(),
				);
			}

			$result[$organizerKey]['events'][] = $event;
		}
		return $this->order($result);
	}

	protected function order($events) {

		if(!$this->arguments['orderBy']) {
			return $events;
		}

		$this->orderGetMethodName = 'get' . GeneralUtility::underscoredToUpperCamelCase($this->arguments['orderBy']);
		if(usort($events, array($this, "orderByObjectMethod"))) {
			return $events;
		} else {
			throw new \RuntimeException(sprintf('%s could not sort the events.', get_class($this)));
		}
	}

	protected $orderGetMethodName = null;

	protected function orderByObjectMethod($a, $b) {
		if(strlen($this->orderGetMethodName) < 5) {
			throw new \UnexpectedValueException(sprintf('%s was called without setting a getMethodName', __FUNCTION__));
		}

		$aValue = call_user_func(array($a['info'], $this->orderGetMethodName));
		$bValue = call_user_func(array($b['info'], $this->orderGetMethodName));

		return $aValue < $bValue ? -1 : ($aValue > $bValue ? 1 : 0);
	}
}
