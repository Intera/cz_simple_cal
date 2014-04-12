<?php
namespace Tx\CzSimpleCal\Controller;

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
 * Controller for the EventIndex object
 */
class EventIndexController extends BaseExtendableController {

	/**
	 * @var \Tx\CzSimpleCal\Domain\Repository\EventIndexRepository
	 * @inject
	 */
	protected $eventIndexRepository;

	/**
	 * builds a list of some events
	 *
	 * @return null
	 */
	public function listAction() {
		$start = $this->getStartDate();
		$end = $this->getEndDate();

		$this->view->assign('start', $start);
		$this->view->assign('end', $end);

		$this->view->assign(
			'events',
			$this->eventIndexRepository->findAllWithSettings(array_merge(
				$this->actionSettings,
				array(
					'startDate' => $start->getTimestamp(),
					'endDate'   => $end->getTimestamp()
				)
			))
		);
	}

	/**
	 * count events and group them by an according timespan
	 */
	public function countEventsAction() {
		$start = $this->getStartDate();
		$end = $this->getEndDate();

		$this->view->assign('start', $start);
		$this->view->assign('end', $end);

		$this->view->assign(
			'data',
			$this->eventIndexRepository->countAllWithSettings(array_merge(
				$this->actionSettings,
				array(
					'startDate' => $start->getTimestamp(),
					'endDate'   => $end->getTimestamp()
				)
			))
		);
	}

	/**
	 * display a single event
	 *
	 * @param integer $event
	 * @return null
	 */
	public function showAction($event) {

		/* don't let Extbase fetch the event
		 * as you won't be able to extend the model
		 * via an extension
		 */
		/** @var \Tx\CzSimpleCal\Domain\Model\EventIndex $eventIndexObject */
		$eventIndexObject = $this->eventIndexRepository->findByUid($event);

		if(empty($eventIndexObject)) {
			$this->throwStatus(404, 'Not found', 'The requested event could not be found.');
		}

		$this->view->assign('event', $eventIndexObject);
	}


	/**
	 * get the start date of events that should be fetched
	 *
	 * @return CzSimpleCalDateTime
	 */
	protected function getStartDate() {
		if(array_key_exists('startDate', $this->actionSettings)) {
			if(isset($this->actionSettings['getDate'])) {
				$date = new CzSimpleCalDateTime($this->actionSettings['getDate']);
				$date->modify($this->actionSettings['startDate']);
			} else {
				$date = new CzSimpleCalDateTime($this->actionSettings['startDate']);
			}
			return $date;
		} else {
			return null;
		}
	}

	/**
	 * get the end date of events that should be fetched
	 *
	 * @todo getDate support
	 * @return CzSimpleCalDateTime
	 */
	protected function getEndDate() {
		if(array_key_exists('endDate', $this->actionSettings)) {
			if(isset($this->actionSettings['getDate'])) {
				$date = new CzSimpleCalDateTime($this->actionSettings['getDate']);
				$date->modify($this->actionSettings['endDate']);
			} else {
				$date = new CzSimpleCalDateTime($this->actionSettings['endDate']);
			}
			return $date;
		} else {
			return null;
		}
	}
}