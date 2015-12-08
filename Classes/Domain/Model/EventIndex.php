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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Tx\CzSimpleCal\Utility\Inflector;
use Tx\CzSimpleCal\Utility\DateTime as CzSimpleCalDateTime;
use Tx\CzSimpleCal\Domain\Model\Enumeration\EventStatus;

/**
 * Event index entry.
 */
class EventIndex extends Base {

	/**
	 * the end date as DateTime object
	 *
	 * @var \Tx\CzSimpleCal\Utility\DateTime
	 */
	protected $dateTimeObjectEnd = NULL;

	/**
	 * the start date as DateTime object
	 *
	 * @var \Tx\CzSimpleCal\Utility\DateTime
	 */
	protected $dateTimeObjectStart = NULL;

	/**
	 * the timestamp from the end of that event
	 *
	 * @ugly integer is used as we'd like an instance of the Utility_DayTime, but extbase would only return a DateTime Object in the extbase version shipped with TYPO3 4.4
	 * @var integer
	 */
	protected $end;

	/**
	 * @var \Tx\CzSimpleCal\Domain\Model\Event
	 */
	protected $event;

	/**
	 * @inject
	 * @transient
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * the pid of the record
	 *
	 * @var integer
	 */
	protected $pid;

	/**
	 * the property slug
	 *
	 * @var string slug
	 */
	protected $slug;

	/**
	 * the timestamp from the beginning of that event
	 *
	 * @ugly integer is used as we'd like an instance of the Utility_DayTime, but extbase would only return a DateTime Object in the extbase version shipped with TYPO3 4.4
	 * @var integer
	 */
	protected $start;

	/**
	 * @var \Tx\CzSimpleCal\Domain\Model\Enumeration\EventStatus
	 */
	protected $status;

	/**
	 * @var string
	 */
	protected $teaser;

	/**
	 * create a new instance with data from a given array
	 *
	 * @param EventIndex $obj
	 * @param $data
	 * @throws \InvalidArgumentException
	 * @return EventIndex
	 */
	public static function fromArray($obj, $data) {

		foreach ($data as $name => $value) {
			$methodName = 'set' . GeneralUtility::underscoredToUpperCamelCase($name);

			// check if there is a setter defined (use of is_callable to check if the scope is public)
			if (!is_callable(array($obj, $methodName))) {
				throw new \InvalidArgumentException(sprintf('Could not find the %s method to set %s in %s.', $methodName, $name, get_class($obj)));
			}

			call_user_func(array($obj, $methodName), $value);
		}

		return $obj;
	}

	/**
	 * generate a slug for this record
	 *
	 * @return string
	 */
	public function generateSlug() {
		$value = $this->generateRawSlug();
		$value = Inflector::urlize($value);

		/** @var \Tx\CzSimpleCal\Domain\Repository\EventIndexRepository $eventIndexRepository */
		$eventIndexRepository = $this->objectManager->get('Tx\\CzSimpleCal\\Domain\\Repository\\EventIndexRepository');

		$slug = $eventIndexRepository->makeSlugUnique($value, $this->uid);
		$this->setSlug($slug);
	}

	/**
	 * get the end of this event as a dateTimeObject
	 *
	 * @return CzSimpleCalDateTime
	 */
	public function getDateTimeObjectEnd() {
		if (is_null($this->dateTimeObjectEnd)) {
			$this->dateTimeObjectEnd = new CzSimpleCalDateTime(
				'@' . $this->end
			);
			$this->dateTimeObjectEnd->setTimezone(new \DateTimeZone(date_default_timezone_get()));
		}
		return $this->dateTimeObjectEnd;
	}

	/**
	 * get the start of this event as a dateTimeObject
	 *
	 * @return CzSimpleCalDateTime
	 */
	public function getDateTimeObjectStart() {
		if (is_null($this->dateTimeObjectStart)) {
			$this->dateTimeObjectStart = new CzSimpleCalDateTime(
				'@' . $this->start
			);
			$this->dateTimeObjectStart->setTimezone(new \DateTimeZone(date_default_timezone_get()));
		}
		return $this->dateTimeObjectStart;
	}

	/**
	 * get the timestamp from the end of that event
	 *
	 * @return integer
	 */
	public function getEnd() {
		return $this->end;
	}

	/**
	 * Returns the related event object.
	 *
	 * @return Event
	 */
	public function getEvent() {
		return $this->event;
	}

	/**
	 * get a hash for this recurrance of the event
	 *
	 * @return string
	 */
	public function getHash() {
		return md5(
			'eventindex-' .
			$this->getEvent()->getHash() . '-' .
			$this->getStart() . '-' .
			$GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']
		);
	}

	/**
	 * getter for slug
	 *
	 * @return string
	 */
	public function getSlug() {
		return $this->slug;
	}

	/**
	 * get the timestamp from the beginning of that event
	 *
	 * @return integer
	 */
	public function getStart() {
		return $this->start;
	}

	/**
	 * If a status was set in this event index entry, return this status,
	 * otherwise return the status of the event.
	 *
	 * @return string
	 */
	public function getStatus() {

		if (!isset($this->status)) {
			/** @noinspection PhpMethodParametersCountMismatchInspection */
			$this->status = $this->objectManager->get(EventStatus::class, EventStatus::UNDEFINED);
		}

		if ($this->status->equals(EventStatus::UNDEFINED)) {
			$status = $this->event->getStatus();
		} else {
			$status = (string)$this->status;
		}

		return $status;
	}

	/**
	 * @return string
	 */
	public function getTeaser() {
		if (isset($this->teaser)) {
			return $this->teaser;
		}
		return $this->event->getTeaser();
	}

	/**
	 * will be called before instance is added to the repository
	 *
	 * @return null
	 */
	public function preCreate() {
		$this->generateSlug();
	}

	/**
	 * set the timestamp from the end of that event
	 *
	 * @param integer $end
	 * @return null
	 */
	public function setEnd($end) {
		$this->end = $end;
		$this->dateTimeObjectEnd = NULL;
	}

	/**
	 * Sets the event and the sys_language.
	 *
	 * @param Event $event
	 */
	public function setEvent($event) {
		$this->event = $event;
		$this->_languageUid = $event->getSysLanguageUid();
	}

	/**
	 * setter for slug
	 *
	 * @param string $slug
	 * @return EventIndex
	 * @throws \InvalidArgumentException
	 */
	public function setSlug($slug) {
		if (preg_match('/^[a-z0-9\-]*$/i', $slug) === FALSE) {
			throw new \InvalidArgumentException(sprintf('"%s" is no valid slug. Only ASCII-letters, numbers and the hyphen are allowed.'));
		}
		$this->slug = $slug;
		return $this;
	}

	/**
	 * set the timestamp from the beginning of that event
	 *
	 * @param integer $start
	 * @return null
	 */
	public function setStart($start) {
		$this->start = $start;
		$this->dateTimeObjectStart = NULL;
	}

	/**
	 * Setter for the status
	 *
	 * @param string $status
	 */
	public function setStatus($status) {
		/** @noinspection PhpMethodParametersCountMismatchInspection */
		$this->status = $this->objectManager->get(EventStatus::class, $status);
	}

	/**
	 * @param string $teaser
	 */
	public function setTeaser($teaser) {
		$this->teaser = $teaser;
	}

	/* Begin event methods, TODO: mark deprecated and remove! */

	/**
	 * @return Location
	 * @deprecated Use ->getEvent()->getActiveLocation() instead.
	 */
	public function getActiveLocation() {
		return $this->getEvent()->getActiveLocation();
	}

	/**
	 * @return Location
	 * @deprecated Use ->getEvent()->getActiveOrganizer() instead.
	 */
	public function getActiveOrganizer() {
		return $this->getEvent()->getActiveOrganizer();
	}

	/**
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<Category> categories
	 * @deprecated Use ->getEvent()->getCategories() instead.
	 */
	public function getCategories() {
		return $this->getEvent()->getCategories();
	}

	/**
	 * @return Category
	 * @deprecated Use ->getEvent()->getCategory() instead.
	 */
	public function getCategory() {
		return $this->getEvent()->getCategory();
	}

	/**
	 * @return int $cruserFe
	 * @deprecated Use ->getEvent()->getCruserFe() instead.
	 */
	public function getCruserFe() {
		return $this->getEvent()->getCruserFe();
	}

	/**
	 * @return string a long description for this event
	 * @deprecated Use ->getEvent()->getDescription() instead.
	 */
	public function getDescription() {
		return $this->getEvent()->getDescription();
	}

	/**
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<Exception> exception
	 * @deprecated Use ->getEvent()->getExceptions() instead.
	 */
	public function getExceptions() {
		return $this->getEvent()->getExceptions();
	}

	/**
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
	 * @deprecated Use ->getEvent()->getFileReferences() instead.
	 */
	public function getFileReferences() {
		return $this->getEvent()->getFileReferences();
	}

	/**
	 * @return File[]
	 * @deprecated Use ->getEvent()->getFileReferences()
	 */
	public function getFiles() {
		return $this->getEvent()->getFiles();
	}

	/**
	 * @return string|false
	 * @deprecated Use ->getEvent()->getFlickrTags() instead.
	 */
	public function getFlickrTags() {
		return $this->getEvent()->getFlickrTags();
	}

	/**
	 * @return array
	 * @deprecated Use ->getEvent()->getFlickrTagsArray() instead.
	 */
	public function getFlickrTagsArray() {
		return $this->getEvent()->getFlickrTagsArray();
	}

	/**
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
	 * @deprecated Use ->getEvent()->getImageReferences() instead.
	 */
	public function getImageReferences() {
		return $this->getEvent()->getImageReferences();
	}

	/**
	 * @return File[]
	 * @deprecated Use ->getEvent()->getImageReferences()
	 */
	public function getImages() {
		return $this->getEvent()->getImages();
	}

	/**
	 * @return \DateTime
	 * @deprecated Use ->getEvent()->getLastIndexed() instead.
	 */
	public function getLastIndexed() {
		return $this->getEvent()->getLastIndexed();
	}

	/**
	 * @return Location
	 * @deprecated Use ->getEvent()->getLocation() instead.
	 */
	public function getLocation() {
		return $this->getEvent()->getLocation();
	}

	/**
	 * @return string the address of the location this event takes place in
	 * @deprecated Please get the property directly from the inline location.
	 */
	public function getLocationAddress() {
		return $this->getEvent()->getLocationAddress();
	}

	/**
	 * @return string $locationCity
	 * @deprecated Please get the property directly from the inline location.
	 */
	public function getLocationCity() {
		return $this->getEvent()->getLocationCity();
	}

	/**
	 * @return string $locationCountry
	 * @deprecated Please get the property directly from the inline location.
	 */
	public function getLocationCountry() {
		return $this->getEvent()->getLocationCountry();
	}

	/**
	 * @param boolean $createDummyLocation
	 * @param boolean $persistDummyLocation
	 * @return Location
	 * @deprecated Use ->getEvent()->getLocationInline() instead.
	 */
	public function getLocationInline($createDummyLocation = FALSE, $persistDummyLocation = FALSE) {
		return $this->getEvent()->getLocationInline($createDummyLocation, $persistDummyLocation);
	}

	/**
	 * @return string the name of the location this event takes place in
	 * @deprecated Please get the property directly from the inline location.
	 */
	public function getLocationName() {
		return $this->getEvent()->getLocationName();
	}

	/**
	 * @return string $locationZip
	 * @deprecated Please get the property directly from the inline location.
	 */
	public function getLocationZip() {
		return $this->getEvent()->getLocationZip();
	}

	/**
	 * @return EventIndex
	 * @deprecated Use ->getEvent()->getNextAppointment() instead.
	 */
	public function getNextAppointment() {
		return $this->getEvent()->getNextAppointment();
	}

	/**
	 * @param $limit
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 * @deprecated Use ->getEvent()->getNextAppointments() instead.
	 */
	public function getNextAppointments($limit = 3) {
		return $this->getEvent()->getNextAppointments($limit);
	}

	/**
	 * @return Organizer
	 * @deprecated Use ->getEvent()->getOrganizer() instead.
	 */
	public function getOrganizer() {
		return $this->getEvent()->getOrganizer();
	}

	/**
	 * @return string $organizerAddress
	 * @deprecated Please get the property directly from the inline organizer.
	 */
	public function getOrganizerAddress() {
		return $this->getEvent()->getOrganizerAddress();
	}

	/**
	 * @return string $organizerCity
	 * @deprecated Please get the property directly from the inline organizer.
	 */
	public function getOrganizerCity() {
		return $this->getEvent()->getOrganizerCity();
	}

	/**
	 * @return string $organizerCountry
	 * @deprecated Please get the property directly from the inline organizer.
	 */
	public function getOrganizerCountry() {
		return $this->getEvent()->getOrganizerCountry();
	}

	/**
	 * @param boolean $createDummyOrganizer
	 * @param boolean $persistDummyOrganizer
	 * @return Organizer
	 * @deprecated Use ->getEvent()->getOrganizerInline() instead.
	 */
	public function getOrganizerInline($createDummyOrganizer = FALSE, $persistDummyOrganizer = FALSE) {
		return $this->getEvent()->getOrganizerInline($createDummyOrganizer, $persistDummyOrganizer);
	}

	/**
	 * @return string the name of the institution or person the event is organized by
	 * @deprecated Please get the property directly from the inline organizer.
	 */
	public function getOrganizerName() {
		return $this->getEvent()->getOrganizerName();
	}

	/**
	 * @return string $organizerZip
	 * @deprecated Please get the property directly from the inline organizer.
	 */
	public function getOrganizerZip() {
		return $this->getEvent()->getOrganizerZip();
	}

	/**
	 * @return array
	 * @deprecated Use ->getEvent()->getRecurrances() instead.
	 */
	public function getRecurrances() {
		return $this->getEvent()->getRecurrances();
	}

	/**
	 * @return string
	 * @deprecated Use ->getEvent()->getShowPageInstead() instead.
	 */
	public function getShowPageInstead() {
		return $this->getEvent()->getShowPageInstead();
	}

	/**
	 * @return string The title of this event
	 * @deprecated Use ->getEvent()->getTitle() instead.
	 */
	public function getTitle() {
		return $this->getEvent()->getTitle();
	}

	/**
	 * @return string
	 * @deprecated Use ->getEvent()->getTwitterHashtags() instead.
	 */
	public function getTwitterHashtags() {
		return $this->getEvent()->getTwitterHashtags();
	}

	/**
	 * @return array
	 * @deprecated Use ->getEvent()->getTwitterHashtagsArray() instead.
	 */
	public function getTwitterHashtagsArray() {
		return $this->getEvent()->getTwitterHashtagsArray();
	}

	/**
	 * @return boolean
	 * @deprecated Use ->getEvent()->isAlldayEvent() instead.
	 */
	public function isAlldayEvent() {
		return $this->getEvent()->isAlldayEvent();
	}

	/**
	 * @return boolean
	 * @deprecated Use ->getEvent()->isEndTimePresent() instead.
	 */
	public function isEndTimePresent() {
		return $this->getEvent()->isEndTimePresent();
	}

	/**
	 * @return boolean
	 * @deprecated Use ->getEvent()->isOneDayEvent() instead.
	 */
	public function isOneDayEvent() {
		return $this->getEvent()->isOneDayEvent();
	}

	/**
	 * @return boolean
	 * @deprecated Use ->getEvent()->isRecurrant() instead.
	 */
	public function isRecurrant() {
		return $this->getEvent()->isRecurrant();
	}

	/* End event methods */

	/**
	 * generate a raw slug that might have invalid characters
	 *
	 * you could overwrite this if you want a different slug
	 *
	 * @return string
	 */
	protected function generateRawSlug() {
		$value = $this->getEvent()->getSlug();
		if ($this->getEvent()->isRecurrant()) {
			$value .= ' ' . $this->getDateTimeObjectStart()->format('Y-m-d');
		}
		return $value;
	}
}