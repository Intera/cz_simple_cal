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

/**
 * An exception for an event in the calendar
 */
class Exception extends BaseEvent {

	/**
	 * The title of this exception
	 * @var string
	 * @validate NotEmpty
	 */
	protected $title;

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Tx\CzSimpleCal\Domain\Model\Event>
	 */
	protected $events;

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Tx\CzSimpleCal\Domain\Model\ExceptionGroup>
	 */
	protected $exceptionGroups;

	/**
	 * Setter for title
	 *
	 * @param string $title The title of this exception
	 * @return void
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Getter for title
	 *
	 * @return string The title of this exception
	 */
	public function getTitle() {
		return $this->title;
	}
}