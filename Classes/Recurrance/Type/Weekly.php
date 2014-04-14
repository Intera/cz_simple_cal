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
class Weekly extends Base {

	/**
	 * the main method building the recurrance
	 *
	 * @return void
	 */
	protected function doBuild() {

		$start = clone $this->event->getDateTimeObjectStart();
		$end = clone $this->event->getDateTimeObjectEnd();
		$until = $this->event->getDateTimeObjectRecurranceUntil();

		$interval = $this->event->getRecurranceSubtype();
		if ($interval === 'weekly') {
			$step = '+1 week';
		} elseif ($interval === 'oddeven') {
			$this->buildOddEven($start, $end, $until);
			return;
		} elseif ($interval === '2week') {
			$step = '+2 week';
		} elseif ($interval === '3week') {
			$step = '+3 week';
		} elseif ($interval === '4week') {
			$step = '+4 week';
		} else {
			$step = '+1 week';
		}

		while ($until >= $start) {

			$this->timeline->add(
				array(
					'start' => $start->getTimestamp(),
					'end' => $end->getTimestamp()
				),
				$this->event
			);

			$start->modify($step);
			$end->modify($step);

		}
	}

	/**
	 * special method to build events taking place on odd or even weeks
	 *
	 * @param CzSimpleCalDateTime $start
	 * @param CzSimpleCalDateTime $end
	 * @param CzSimpleCalDateTime $until
	 */
	protected function buildOddEven($start, $end, $until) {

		$week = $start->format('W') % 2;
		while ($until >= $start) {

			$this->timeline->add(
				array(
					'start' => $start->getTimestamp(),
					'end' => $end->getTimestamp()
				),
				$this->event
			);

			$start->modify('+2 week');
			$end->modify('+2 week');

			// take care of year switches
			if ($start->format('W') % 2 !== $week) {
				$start->modify('-1 week');
				$end->modify('-1 week');
			}
		}
	}

	/**
	 * get the configured subtypes of this recurrance
	 *
	 * @return array
	 */
	public function getSubtypes() {
		return self::addLL(array('weekly', 'oddeven', '2week', '3week', '4week'));
	}
}