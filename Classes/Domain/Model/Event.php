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

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Tx\CzSimpleCal\Utility\FileArrayBuilder;
use Tx\CzSimpleCal\Utility\Inflector;
use Tx\CzSimpleCal\Recurrance\RecurranceFactory;

/**
 * An event in an calendar
 */
class Event extends BaseEvent {

	/**
	 * an array of fields that if changed require a reindexing of all the events
	 *
	 * @var array
	 */
	protected static $fieldsRequiringReindexing = array(
		'recurrance_type',
		'recurrance_subtype',
		'recurrance_until',
		'start_day',
		'start_time',
		'end_day',
		'end_time',
		'pid',
		'hidden',
		'deleted'
	);

	/**
	 * the page-id this domain model resides on
	 * @var integer
	 */
	protected $pid;


	/**
	 * The title of this event
	 * @var string
	 * @validate NotEmpty, StringLength(minimum=3,maximum=255)
	 */
	protected $title;

	/**
	 * a short teaser for this event
	 * @var string
	 * @validate String
	 */
	protected $teaser;

	/**
	 * a long description for this event
	 * @var string
	 * @validate String
	 */
	protected $description;

	/**
	 * The location of the event. This record is used in multiple
	 * events (selected by element browser).
	 *
	 * This setting has precedence before $locationInline.
	 *
	 * @lazy
	 * @var \Tx\CzSimpleCal\Domain\Model\Location
	 */
	protected $location;

	/**
	 * The location of the event. This record is only used in the
	 * current event (inline element).
	 *
	 * @lazy
	 * @var \Tx\CzSimpleCal\Domain\Model\Location
	 */
	protected $locationInline;

	/**
	 * The organizer of the event. This record is used in multiple
	 * events (selected by element browser).
	 *
	 * This setting has precedence before $organizerInline.
	 *
	 * @lazy
	 * @var \Tx\CzSimpleCal\Domain\Model\Organizer
	 */
	protected $organizer;

	/**
	 * The organizer of the event. This record is only used in the
	 * current event (inline element).
	 *
	 * @lazy
	 * @var \Tx\CzSimpleCal\Domain\Model\Organizer
	 */
	protected $organizerInline;

	/**
	 * categories
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Tx\CzSimpleCal\Domain\Model\Category>
	 */
	protected $categories;

	/**
	 * Exceptions for this event
	 *
	 * @lazy
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Tx\CzSimpleCal\Domain\Model\Exception>
	 */
	protected $exceptions;

	/**
	 * Cache for all exceptions retrieved from exceptions and exeption groups.
	 *
	 * @transient
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Tx\CzSimpleCal\Domain\Model\Exception>
	 */
	protected $exceptionCache;

	/**
	 * Exception groups for this event
	 *
	 * @lazy
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Tx\CzSimpleCal\Domain\Model\ExceptionGroup>
	 */
	protected $exceptionGroups;

	/**
	 * is this record hidden
	 *
	 * @var boolean
	 */
	protected $hidden;

	/**
	 * is this record deleted
	 *
	 * @var boolean
	 */
	protected $deleted;

	/**
	 * the image files associated with this event
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
	 * @lazy
	 */
	protected $images;

	/**
	 * files associated with this event
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
	 * @lazy
	 */
	protected $files;

	/**
	 * @var int cruserFe
	 */
	protected $cruserFe;

	/**
	 * Status of the event.
	 *
	 * @var \Tx\CzSimpleCal\Domain\Model\Enumeration\EventStatus
	 */
	protected $status;

	/**
	 * @inject
	 * @transient
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @var \DateTime
	 */
	protected $tstamp;

	/**
	 * Setter for title
	 *
	 * @param string $title The title of this event
	 * @return void
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Getter for title
	 *
	 * @return string The title of this event
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Setter for teaser
	 *
	 * @param string $teaser a short teaser for this event
	 * @return void
	 */
	public function setTeaser($teaser) {
		$this->teaser = $teaser;
	}

	/**
	 * Getter for teaser
	 *
	 * @return string a short teaser for this event
	 */
	public function getTeaser() {
		return $this->teaser;
	}

	/**
	 * Setter for description
	 *
	 * @param string $description a long description for this event
	 * @return void
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * Getter for description
	 *
	 * @return string a long description for this event
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Setter for locationName
	 *
	 * @param string $locationName the name of the location this event takes place in
	 * @return void
	 * @deprecated Please set the property directly in the inline location.
	 */
	public function setLocationName($locationName) {
		$this->getLocationInline(TRUE, TRUE)->setName($locationName);
	}

	/**
	 * Getter for locationName
	 *
	 * @return string the name of the location this event takes place in
	 * @deprecated Please get the property directly from the inline location.
	 */
	public function getLocationName() {
		return $this->getLocationInline(TRUE)->getName();
	}

	/**
	 * Setter for locationAddress
	 *
	 * @param string $locationAddress the address of the location this event takes place in
	 * @return void
	 * @deprecated Please set the property directly in the inline location.
	 */
	public function setLocationAddress($locationAddress) {
		$this->getLocationInline(TRUE, TRUE)->setAddress($locationAddress);
	}

	/**
	 * Getter for locationAddress
	 *
	 * @return string the address of the location this event takes place in
	 * @deprecated Please get the property directly from the inline location.
	 */
	public function getLocationAddress() {
		return $this->getLocationInline(TRUE)->getAddress();
	}

	/**
	 * Setter for locationZip
	 *
	 * @param string $locationZip
	 * @return void
	 * @deprecated Please set the property directly in the inline location.
	 */
	public function setLocationZip($locationZip) {
		$this->getLocationInline(TRUE, TRUE)->setZip($locationZip);
	}

	/**
	 * Getter for locationZip
	 *
	 * @return string $locationZip
	 * @deprecated Please get the property directly from the inline location.
	 */
	public function getLocationZip() {
		return $this->getLocationInline(TRUE)->getZip();
	}


	/**
	 * Setter for locationCity
	 *
	 * @param string $locationCity
	 * @return void
	 * @deprecated Please set the property directly in the inline location.
	 */
	public function setLocationCity($locationCity) {
		$this->getLocationInline(TRUE, TRUE)->setCity($locationCity);
	}

	/**
	 * Getter for locationCity
	 *
	 * @return string $locationCity
	 * @deprecated Please get the property directly from the inline location.
	 */
	public function getLocationCity() {
		return $this->getLocationInline(TRUE)->getCity();
	}


	/**
	 * Setter for locationCountry
	 *
	 * @param string $locationCountry
	 * @return void
	 * @deprecated Please set the property directly in the inline location.
	 */
	public function setLocationCountry($locationCountry) {
		$this->getLocationInline(TRUE, TRUE)->setCountry($locationCountry);
	}

	/**
	 * Getter for locationCountry
	 *
	 * @return string $locationCountry
	 * @deprecated Please get the property directly from the inline location.
	 */
	public function getLocationCountry() {
		return $this->getLocationInline(TRUE)->getCountry();
	}

	/**
	 * Getter for status.
	 *
	 * This method returns a string and no instance of the EventStatus
	 * enumeration to prevent problems when the value is used in Fluid
	 * template (e.g. in conditions).
	 *
	 * @return string
	 */
	public function getStatus() {
		if (!isset($this->status)) {
			$this->status = $this->objectManager->get('Tx\\CzSimpleCal\\Domain\\Model\\Enumeration\\EventStatus');
		}
		return (string)$this->status;
	}



	/**
	 * Setter for organizerName
	 *
	 * @param string $organizerName the name of the institution or person the event is organized by
	 * @return void
	 * @deprecated Please set the property directly in the inline organizer.
	 */
	public function setOrganizerName($organizerName) {
		$this->getOrganizerInline(TRUE, TRUE)->setName($organizerName);
	}


	/**
	 * Setter for organizerAddress
	 *
	 * @param string $organizerAddress
	 * @return void
	 * @deprecated Please set the property directly in the inline organizer.
	 */
	public function setOrganizerAddress($organizerAddress) {
		$this->getOrganizerInline(TRUE, TRUE)->setAddress($organizerAddress);
	}

	/**
	 * Getter for organizerAddress
	 *
	 * @return string $organizerAddress
	 * @deprecated Please get the property directly from the inline organizer.
	 */
	public function getOrganizerAddress() {
		return $this->getOrganizerInline(TRUE)->getAddress();
	}


	/**
	 * Setter for organizerZip
	 *
	 * @param string $organizerZip
	 * @return void
	 * @deprecated Please set the property directly in the inline organizer.
	 */
	public function setOrganizerZip($organizerZip) {
		$this->getOrganizerInline(TRUE,TRUE)->setZip($organizerZip);
	}

	/**
	 * Getter for organizerZip
	 *
	 * @return string $organizerZip
	 * @deprecated Please get the property directly from the inline organizer.
	 */
	public function getOrganizerZip() {
		return $this->getOrganizerInline(TRUE)->getZip();
	}


	/**
	 * Setter for organizerCity
	 *
	 * @param string $organizerCity
	 * @return void
	 * @deprecated Please set the property directly in the inline organizer.
	 */
	public function setOrganizerCity($organizerCity) {
		$this->getOrganizerInline(TRUE, TRUE)->setCity($organizerCity);
	}

	/**
	 * Getter for organizerCity
	 *
	 * @return string $organizerCity
	 * @deprecated Please get the property directly from the inline organizer.
	 */
	public function getOrganizerCity() {
		return $this->getOrganizerInline(TRUE)->getCity();
	}


	/**
	 * Setter for organizerCountry
	 *
	 * @param string $organizerCountry
	 * @return void
	 * @deprecated Please set the property directly in the inline organizer.
	 */
	public function setOrganizerCountry($organizerCountry) {
		$this->getOrganizerInline(TRUE, TRUE)->setCountry($organizerCountry);
	}

	/**
	 * Getter for organizerCountry
	 *
	 * @return string $organizerCountry
	 * @deprecated Please get the property directly from the inline organizer.
	 */
	public function getOrganizerCountry() {
		return $this->getOrganizerInline(TRUE)->getCountry();
	}


	/**
	 * Getter for organizerName
	 *
	 * @return string the name of the institution or person the event is organized by
	 * @deprecated Please get the property directly from the inline organizer.
	 */
	public function getOrganizerName() {
		return $this->getOrganizerInline(TRUE)->getName();
	}

	/**
	 * Setter for category
	 *
	 * @param ObjectStorage $categories
	 * @return void
	 */
	public function setCategories(ObjectStorage $categories = NULL) {
		$this->categories = $categories;
	}

	/**
	 * Getter for category
	 *
	 * @return ObjectStorage<Category> categories
	 */
	public function getCategories() {
		return $this->categories;
	}

	/**
	 * getter for the first category
	 *
	 * @return Category
	 */
	public function getCategory() {
		if(is_null($this->categories) || $this->categories->count() === 0) {
			return null;
		}
		$this->categories->rewind();
		return $this->categories->current();
	}

	/**
	 * Adds a Category
	 *
	 * @param Category $category The Category to be added
	 * @return void
	 */
	public function addCategory(Category $category) {
		if(!is_object($this->categories)) {
			$this->categories = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage');
		}
		$this->categories->attach($category);
	}

	/**
	 * Removes a Category
	 *
	 * @param Category $category The Category to be removed
	 * @return void
	 */
	public function removeCategory(Category $category) {
		$this->categories->detach($category);
	}

	/**
	 * Getter for exceptions
	 *
	 * Extbase internal functionality can't be used here as
	 * the records need to be fetched from two different tables
	 *
	 * @return ObjectStorage<Exception> exception
	 */
	public function getExceptions() {

		if (isset($this->exceptionCache)) {
			return $this->exceptionCache;
		}

		/** @var ObjectStorage $exceptionCache */
		$exceptionCache = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage');

		if (isset($this->exceptions)) {
			$exceptionCache->addAll($this->exceptions);
		}

		if (isset($this->exceptionGroups)) {
			/** @var ExceptionGroup $exceptionGroup */
			foreach ($this->exceptionGroups as $exceptionGroup) {
				$exceptions = $exceptionGroup->getExceptions();
				if (isset($exceptions)) {
					$exceptionCache->addAll($exceptions);
				}
			}
		}

		$this->exceptionCache = $exceptionCache;
		return $this->exceptionCache;
	}

	/**
	 * get all recurrances of this event
	 *
	 * @return array
	 */
	public function getRecurrances() {
		$factory = new RecurranceFactory();
		return $factory->buildRecurranceForEvent($this);
	}

	/**
	 * check if this is an allday event
	 *
	 * @return boolean
	 */
	public function isAlldayEvent() {
		return $this->startTime === NULL;
	}

	/**
	 * check if this event has an endtime set
	 *
	 * @return boolean
	 */
	public function hasEndTime() {
		return $this->endTime !== NULL;
	}

	/**
	 * check if this event is on one day only
	 *
	 * @return boolean
	 */
	public function isOneDayEvent() {
		return $this->endDay === NULL || $this->endDay === $this->startDay;
	}

	/**
	 * check if this record is enabled
	 *
	 * @return boolean
	 */
	public function isEnabled() {
		return !$this->hidden && !$this->deleted;
	}

	/**
	 * returns true if this is a recurrant event
	 *
	 * @return boolean
	 */
	public function isRecurrant() {
		return !empty($this->recurranceType) && $this->recurranceType !== 'none';
	}

	/**
	 * Getter for the inline organizer.
	 *
	 * If no inline organizer is present a dummy organizer will be created
	 * on the fly.
	 *
	 * @param boolean $createDummyOrganizer Internal use only! If this
	 * is TRUE a dummy organizer will be initalized if no inline
	 * organizer was set. For use with getter methods.
	 * @param boolean $persistDummyOrganizer Internal use only! If this
	 * is TRUE the dummy organizer will be set to the class variable so
	 * that is will be persisted. For use with setter methods.
	 * @return Organizer
	 */
	public function getOrganizerInline($createDummyOrganizer = FALSE, $persistDummyOrganizer = FALSE) {

		$organizer = $this->organizerInline;

		if (!isset($organizer) && $createDummyOrganizer) {
			/** @var Organizer $organizer */
			$organizer = $this->objectManager->get('Tx\\CzSimpleCal\\Domain\\Model\\Organizer');
			if ($persistDummyOrganizer) {
				$this->organizerInline = $organizer;
			}
		}
		return $organizer;
	}

	/**
	 * Setter for the inline organizer record.
	 *
	 * @param Organizer $organizerInline
	 */
	public function setOrganizerInline($organizerInline) {
		$this->organizerInline = $organizerInline;
	}

	/**
	 * get the organizer of the event
	 *
	 * @return Organizer
	 */
	public function getOrganizer() {
		return $this->organizer;
	}

	/**
	 * setter for organizer
	 *
	 * @param Organizer $organizer
	 * @return Event
	 */
	public function setOrganizer($organizer) {
		$this->organizer = $organizer;
		return $this;
	}

	/**
	 * If a common organizer was set it will be returned. Otherwise
	 * the inline organizer will be returned.
	 *
	 * @return Location
	 */
	public function getActiveOrganizer() {

		$organizer = $this->getOrganizer();

		if (isset($organizer)) {
			return $organizer;
		}

		return $this->getOrganizerInline();
	}

	/**
	 * Getter for the inline location.
	 *
	 * If no inline location is present a dummy location will be created
	 * on the fly.
	 *
	 * @param boolean $createDummyLocation Internal use only! If this
	 * is TRUE a dummy location will be initalized if no inline
	 * location was set. For use with getter methods.
	 * @param boolean $persistDummyLocation Internal use only! If this
	 * is TRUE the dummy location will be set to the class variable so
	 * that is will be persisted. For use with setter methods.
	 * @return Location
	 */
	public function getLocationInline($createDummyLocation = FALSE, $persistDummyLocation = FALSE) {

		$location = $this->locationInline;

		if (!isset($location) && $createDummyLocation) {
			/** @var Location $location */
			$location = $this->objectManager->get('Tx\\CzSimpleCal\\Domain\\Model\\Location');
			if ($persistDummyLocation) {
				$this->locationInline = $location;
			}
		}
		return $location;
	}

	/**
	 * Setter for the inline location record.
	 *
	 * @param Location $locationInline
	 */
	public function setLocationInline($locationInline) {
		$this->locationInline = $locationInline;
	}

	/**
	 * getter for location
	 *
	 * @return Location
	 */
	public function getLocation() {
		return $this->location;
	}

	/**
	 * If a common location was set it will be returned. Otherwise
	 * the inline location will be returned.
	 *
	 * @return Location
	 */
	public function getActiveLocation() {

		$location = $this->getLocation();

		if (isset($location)) {
			return $location;
		}

		return $this->getLocationInline();
	}

	/**
	 * setter for location
	 *
	 * @param Location $location
	 * @return Event
	 */
	public function setLocation($location) {
		$this->location = $location;
		return $this;
	}

	/**
	 * an array of fields that if changed require a reindexing of all the events
	 *
	 * @return array
	 */
	public static function getFieldsRequiringReindexing() {
		return self::$fieldsRequiringReindexing;
	}

	/**
	 * get a hash for this recurrance of the event
	 *
	 * @return string
	 */
	public function getHash() {
		return md5(
			'event-'.
			$this->getUid().
			$GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']
		);
	}

	/**
	 * the property slug
	 *
	 * @var string slug
	 */
	protected $slug;

	/**
	 * getter for slug
	 *
	 * @return string
	 */
	public function getSlug() {
		return $this->slug;
	}

	/**
	 * setter for slug
	 *
	 * @param string $slug
	 * @return Event
	 * @throws \InvalidArgumentException
	 */
	public function setSlug($slug) {
		if(preg_match('/^[a-z0-9\-]*$/i', $slug) === false) {
			throw new \InvalidArgumentException(sprintf('"%s" is no valid slug. Only ASCII-letters, numbers and the hyphen are allowed.'));
		}
		$this->slug = $slug;
		return $this;
	}

	/**
	 * generate a slug for this record
	 *
	 * @return string
	 */
	public function generateSlug() {
		$value = $this->generateRawSlug();
		$value = Inflector::urlize($value);

		/** @var \Tx\CzSimpleCal\Domain\Repository\EventRepository $eventRepository */
		$eventRepository = $this->objectManager->get('Tx\\CzSimpleCal\\Domain\\Repository\\EventRepository');
		$slug = $eventRepository->makeSlugUnique($value, $this->uid);
		$this->setSlug($slug);
	}

	/**
	 *
	 * @return \DateTime
	 */
	public function getTstamp() {
		return $this->tstamp;
	}

	/**
	 * generate a raw slug that might have invalid characters
	 *
	 * you could overwrite this if you want a different slug
	 *
	 * @return string
	 */
	protected function generateRawSlug() {
		return $this->getTitle();
	}

	/**
	 * the property lastIndexed
	 *
	 * @var \DateTime lastIndexed
	 */
	protected $lastIndexed;

	/**
	 * getter for lastIndexed
	 *
	 * @return \DateTime
	 */
	public function getLastIndexed() {
		return $this->lastIndexed;
	}

	/**
	 * setter for lastIndexed
	 *
	 * @param \DateTime $lastIndexed
	 * @return Event
	 */
	public function setLastIndexed($lastIndexed) {
		$this->lastIndexed = $lastIndexed;
		return $this;
	}

	/**
	 * an array of cached next appointments
	 *
	 * @var array
	 */
	protected $nextAppointments = null;

	/**
	 * counts the number of requested nextAppointments
	 *
	 * this is used to check if a database query has to been done
	 * of if the current result set can be taken
	 *
	 * @var integer
	 */
	protected $nextAppointmentsCount = 0;

	/**
	 * get a list of next appointments
	 *
	 * @param $limit
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function getNextAppointments($limit = 3) {
		if(is_null($this->nextAppointments) || $this->nextAppointmentsCount < $limit) {
			/** @var \Tx\CzSimpleCal\Domain\Repository\EventIndexRepository $eventIndexRepository */
			$eventIndexRepository = $this->objectManager->get('Tx\\CzSimpleCal\\Domain\\Repository\\EventIndexRepository');
			$this->nextAppointments = $eventIndexRepository->
				findNextAppointmentsByEventUid($this->getUid(), $limit)
			;
			$this->nextAppointmentsCount = $limit;
		}
		if($this->nextAppointmentsCount === $limit) {
			return $this->nextAppointments;
		} else {
			return array_slice($this->nextAppointments, 0, $limit);
		}
	}

	/**
	 * get the next appointment of this event if any
	 *
	 * @return EventIndex
	 */
	public function getNextAppointment() {
		$appointments = $this->getNextAppointments(1);
		return empty($appointments) ? null : $appointments->getFirst();
	}

	/**
	 * if respected by the template a TYPO3 page is linked instead of the Event:show action
	 *
	 * @var string showPageInstead
	 */
	protected $showPageInstead;

	/**
	 * getter for showPageInstead
	 *
	 * @return string
	 */
	public function getShowPageInstead() {
		return $this->showPageInstead;
	}

	/**
	 * setter for showPageInstead
	 *
	 * @param string $showPageInstead
	 * @return Event
	 */
	public function setShowPageInstead($showPageInstead) {
		if(!empty($showPageInstead) && !is_numeric($showPageInstead) && strpos($showPageInstead, '://') === false) {
			$showPageInstead = 'http://'.$showPageInstead;
		}
		$this->showPageInstead = $showPageInstead;
		return $this;
	}

	/**
	 * an array used internally to cache the images as an array
	 *
	 * @var array
	 */
	protected $_cache_images = null;

	/**
	 * get all images as an array
	 *
	 * @return File[]
	 * @deprecated Use getImageReferences()
	 */
	public function getImages() {
		if(is_null($this->_cache_images)) {
			$this->_cache_images = FileArrayBuilder::buildFromReferences($this->files, TRUE);
		}
		return $this->_cache_images;
	}

	/**
	 * Returns the image file references.
	 *
	 * @return ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
	 */
	public function getImageReferences() {
		return $this->images;
	}

	/**
	 * an array used internally to cache the files as an array
	 *
	 * @var array
	 */
	protected $_cache_files = null;

	/**
	 * get all files as an array
	 *
	 * @return File[]
	 * @deprecated Use getFileReferences()
	 */
	public function getFiles() {
		if (is_null($this->_cache_files)) {
			$this->_cache_files = FileArrayBuilder::buildFromReferences($this->files);
		}
		return $this->_cache_files;
	}

	/**
	 * Returns all related file references.
	 *
	 * @return ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
	 * @deprecated Use getFileReferences()
	 */
	public function getFileReferences() {
		return $this->files;
	}

	/**
	 * @var string
	 */
	protected $twitterHashtags = null;

	/**
	 * a cached array of twitter hashtags
	 * @var array
	 */
	protected $twitterHashtags_ = null;

	/**
	 * @var string
	 */
	protected $flickrTags = null;

	/**
	 * a cached array of flickr hashtags
	 * @var array
	 */
	protected $flickrTags_ = null;

	/**
	 * get an array of twitter hashtags used for this event
	 *
	 * @return array
	 */
	public function getTwitterHashtagsArray() {
		if(is_null($this->twitterHashtags_)) {
			$this->buildTwitterHashtags();
		}
		return $this->twitterHashtags_;
	}

	/**
	 * get the twitterHashtags as string
	 *
	 * @return string
	 */
	public function getTwitterHashtags() {
		return $this->twitterHashtags;
	}

	public function setTwitterHashtags($twitterHashtags) {
		$this->twitterHashtags = $twitterHashtags;
		$this->twitterHashtags_ = null;
	}

	/**
	 * build the array of twitter hashtags
	 *
	 * @return null
	 */
	protected function buildTwitterHashtags() {
		$this->twitterHashtags_ = GeneralUtility::trimExplode(',', $this->twitterHashtags, true);

		// make sure each tag starts with a hash ("#")
		foreach($this->twitterHashtags_ as &$hashtag) {
			if(strncmp($hashtag, '#', 1) !== 0) {
				$hashtag = '#'.$hashtag;
			}
		}
	}

	/**
	 * get an array of flickr tags
	 *
	 * @return array
	 */
	public function getFlickrTagsArray() {
		if(is_null($this->flickrTags_)) {
			$this->buildFlickrTags();
		}
		return $this->flickrTags_;
	}

	/**
	 * get the flickr tags as string
	 *
	 * @return string|false
	 */
	public function getFlickrTags() {
		return $this->flickrTags;
	}


	public function setFlickrTags($flickrTags) {
		$this->flickrTags = $flickrTags;
		$this->flickrTags_ = null;
	}

	/**
	 * build the array of flickr tags
	 * @return null
	 */
	protected function buildFlickrTags() {
		$this->flickrTags_ = GeneralUtility::trimExplode(',', $this->flickrTags, true);
	}


	/**
	 * Setter for cruserFe
	 *
	 * @param int $cruserFe
	 * @return void
	 */
	public function setCruserFe($cruserFe) {
		$this->cruserFe = $cruserFe;
	}

	/**
	 * Getter for cruserFe
	 *
	 * @return int $cruserFe
	 */
	public function getCruserFe() {
		return $this->cruserFe;
	}
}