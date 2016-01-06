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

use Tx\CzSimpleCal\Recurrance\BuildException;
use Tx\CzSimpleCal\Utility\DateTime as CzSimpleCalDateTime;

/**
 * monthly recurrance
 */
class Monthly extends Base {

	const SUBTYPE_AUTO = 'auto';
	const SUBTYPE_BY_DAY_OF_MONTH = 'bydayofmonth';
	const SUBTYPE_1ST_WEEKDAY_OF_MONTH = 'firstweekdayofmonth';
	const SUBTYPE_2ND_WEEKDAY_OF_MONTH = 'secondweekdayofmonth';
	const SUBTYPE_3RD_WEEKDAY_OF_MONTH = 'thirdweekdayofmonth';
	const SUBTYPE_LAST_WEEKDAY_OF_MONTH = 'lastweekdayofmonth';
	const SUBTYPE_PENULTIMATE_WEEKDAY_OF_MONTH = 'penultimateweekdayofmonth';

	/**
	 * the main method building the recurrance
	 *
	 * @return void
	 * @throws \InvalidArgumentException
	 */
	protected function doBuild() {

		$type = $this->event->getRecurranceSubtype();

		if (($type === 'bydayofmonth')) {
			$this->buildByDay();
			return;
		} elseif (strpos($type, 'weekdayofmonth') !== FALSE) {
			if ($type === 'firstweekdayofmonth') {
				$param = 1;
			} elseif ($type === 'lastweekdayofmonth') {
				$param = -1;
			} elseif ($type === 'secondweekdayofmonth') {
				$param = 2;
			} elseif ($type === 'thirdweekdayofmonth') {
				$param = 3;
			} elseif ($type === 'penultimateweekdayofmonth') {
				$param = -2;
			} else {
				throw new \InvalidArgumentException('Subtype is invalid: ' . $type);
			}

			$this->buildByWeekday($param);
			return;
		}

		/** @var CzSimpleCalDateTime $start */
		$start = clone $this->event->getDateTimeObjectStart();
		$day = $start->format('d');

		$start->modify('last day of this month');
		$daysInMonth = $start->format('d');

		if ($day <= 7) {
			$param = 1;
		} elseif ($day <= 14) {
			$param = 2;
		} elseif ($daysInMonth - $day < 7) {
			$param = -1;
		} elseif ($daysInMonth - $day < 14) {
			$param = -2;
		} else {
			$param = 3;
		}
		$this->buildByWeekday($param);
	}

	protected function buildByWeekday($pos) {
		$start = clone $this->event->getDateTimeObjectStart();
		$end = clone $this->event->getDateTimeObjectEnd();
		$until = $this->event->getDateTimeObjectRecurranceUntil();

		$diff = $end->getTimestamp() - $start->getTimestamp();

		while ($until >= $start) {

			$this->timeline->add(
				array(
					'start' => $start->getTimestamp(),
					'end' => $end->getTimestamp()
				),
				$this->event
			);

			$this->advanceOneMonthByWeekday($start, $pos);
			$end = clone $start;
			$end->modify(sprintf('+ %d seconds', $diff));
		}
	}

	/**
	 * @param CzSimpleCalDateTime $date
	 * @param int $pos
	 */
	protected function advanceOneMonthByWeekday($date, $pos) {
		if ($pos > 0) {
			$date->modify('first day of next month|' . $date->format('l H:i:s'));
			if ($pos > 1) {
				$date->modify(sprintf('+%d weeks', $pos - 1));
			}
		} else {
			$date->modify(sprintf('last day of next month|next %s| %d weeks', $date->format('l H:i:s'), $pos));
		}
	}

	protected function buildByDay() {
		$start = clone $this->event->getDateTimeObjectStart();
		$end = clone $this->event->getDateTimeObjectEnd();
		$until = $this->event->getDateTimeObjectRecurranceUntil();

		if ($start->format('j') > 28 || $end->format('j') > 28) {
			throw new BuildException('The day of month of the start or the end was larger than 28. Abortion as this might lead to unexpected results.');
		}

		while ($until >= $start) {

			$this->timeline->add(
				array(
					'start' => $start->getTimestamp(),
					'end' => $end->getTimestamp()
				),
				$this->event
			);

			$start->modify('+1 month');
			$end->modify('+1 month');
		}
	}

	/**
	 * get the configured subtypes of this recurrance
	 *
	 * @return array
	 */
	public function getSubtypes() {
		return self::addLL(array(
			static::SUBTYPE_AUTO,
			static::SUBTYPE_BY_DAY_OF_MONTH,
			static::SUBTYPE_1ST_WEEKDAY_OF_MONTH,
			static::SUBTYPE_2ND_WEEKDAY_OF_MONTH,
			static::SUBTYPE_3RD_WEEKDAY_OF_MONTH,
			static::SUBTYPE_LAST_WEEKDAY_OF_MONTH,
			static::SUBTYPE_PENULTIMATE_WEEKDAY_OF_MONTH,
		));
	}
}