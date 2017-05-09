<?php
namespace Tx\CzSimpleCal\ViewHelpers\Widget\EventIndex\Controller;

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

use Tx\CzSimpleCal\Domain\Repository\EventIndexRepository;
use TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetController;

/**
 * Controller for the list widget.
 */
class ListController extends AbstractWidgetController {

	/**
	 * @var EventIndexRepository
	 */
	protected $eventIndexRepository;

	/**
	 * the action settings to use for fetching the events
	 *
	 * @var array
	 */
	protected $actionSettings = array();

	/**
	 * @return void
	 */
	public function initializeAction() {
		foreach(array('maxEvents', 'order', 'orderBy', 'includeStartedEvents', 'excludeOverlongEvents', 'filter') as $argumentName) {
			if(isset($this->widgetConfiguration[$argumentName])) {
				$this->actionSettings[$argumentName] = $this->widgetConfiguration[$argumentName];
			}
		}
		foreach(array('startDate', 'endDate') as $argumentName) {
			if(isset($this->widgetConfiguration[$argumentName])) {
				$this->actionSettings[$argumentName] = $this->normalizeArgumentToTimestamp($this->widgetConfiguration[$argumentName]);
			}
		}
	}

	public function injectEventIndexRepository(EventIndexRepository $eventIndexRepository) {
		$this->eventIndexRepository = $eventIndexRepository;
	}

	/**
	 * normalizes anything that describes a time
	 * and sets it to be a timestamp
	 *
	 * @param mixed $value
	 * @return int
	 */
	protected function normalizeArgumentToTimestamp($value) {
		if(empty($value)) {
			return 0;
		} elseif(is_numeric($value)) {
			return \TYPO3\CMS\Core\Utility\MathUtility::forceIntegerInRange($value, 0);
		} elseif(is_string($value)) {
			return \Tx\CzSimpleCal\Utility\StrToTime::strtotime($value);
		} elseif($value instanceof \DateTime) {
			return intval($value->format('U'));
		}
		return 0;
	}

	/**
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign(
			'events',
			$this->eventIndexRepository->findAllWithSettings($this->actionSettings)
		);
	}
}