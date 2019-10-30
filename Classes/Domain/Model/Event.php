<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */
/** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */

declare(strict_types=1);

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

use Tx\CzSimpleCal\Domain\Model\Enumeration\EventStatus;
use Tx\CzSimpleCal\Domain\Repository\EventIndexRepository;
use Tx\CzSimpleCal\Domain\Repository\EventRepository;
use Tx\CzSimpleCal\Recurrance\RecurranceFactory;
use Tx\CzSimpleCal\Recurrance\Timeline\Event as TimelineEvent;
use Tx\CzSimpleCal\Utility\FileArrayBuilder;
use Tx\CzSimpleCal\Utility\Inflector;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * An event in an calendar
 */
class Event extends BaseEvent
{
    /**
     * an array of fields that if changed require a reindexing of all the events
     *
     * @var array
     */
    protected static $fieldsRequiringReindexing = [
        'recurrance_type',
        'recurrance_subtype',
        'recurrance_until',
        'start_day',
        'start_time',
        'end_day',
        'end_time',
        'pid',
        'hidden',
        'deleted',
    ];

    /**
     * an array used internally to cache the files as an array
     *
     * @var File[]|array
     */
    protected $_cache_files = null;

    /**
     * an array used internally to cache the images as an array
     *
     * @var array
     */
    protected $_cache_images = null;

    /**
     * categories
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Tx\CzSimpleCal\Domain\Model\Category>
     */
    protected $categories;

    /**
     * @var int cruserFe
     */
    protected $cruserFe;

    /**
     * is this record deleted
     *
     * @var boolean
     */
    protected $deleted;

    /**
     * a long description for this event
     *
     * @var string
     * @Extbase\Validate("String")
     */
    protected $description;

    /**
     * The time when the event will be hidden in the Frontend.
     *
     * @var \DateTime
     */
    protected $enableEndtime;

    /**
     * Cache for all exceptions retrieved from exceptions and exeption groups.
     *
     * @Extbase\ORM\Transient
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
     * Exceptions for this event
     *
     * @lazy
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Tx\CzSimpleCal\Domain\Model\Exception>
     */
    protected $exceptions;

    /**
     * files associated with this event
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     * @lazy
     */
    protected $files;

    /**
     * @var string
     */
    protected $flickrTags = null;

    /**
     * a cached array of flickr hashtags
     *
     * @var array
     */
    protected $flickrTags_ = null;

    /**
     * is this record hidden
     *
     * @var boolean
     */
    protected $hidden;

    /**
     * the image files associated with this event
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     * @lazy
     */
    protected $images;

    /**
     * the property lastIndexed
     *
     * @var \DateTime lastIndexed
     */
    protected $lastIndexed;

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
     *
     * @Extbase\ORM\Transient
     * @var ObjectManagerInterface
     */
    protected $objectManager;

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
     * the page-id this domain model resides on
     *
     * @var integer
     */
    protected $pid;

    /**
     * if respected by the template a TYPO3 page is linked instead of the Event:show action
     *
     * @var string showPageInstead
     */
    protected $showPageInstead;

    /**
     * the property slug
     *
     * @var string slug
     */
    protected $slug;

    /**
     * Status of the event.
     *
     * @var \Tx\CzSimpleCal\Domain\Model\Enumeration\EventStatus
     */
    protected $status;

    /**
     * a short teaser for this event
     *
     * @var string
     * @Extbase\Validate("String")
     */
    protected $teaser;

    /**
     * The title of this event
     *
     * @var string
     * @Extbase\Validate("StringLength", options={"minimum": 3, "maximum": 255})
     * @Extbase\Validate("NotEmpty")
     */
    protected $title;

    /**
     * @var \DateTime
     */
    protected $tstamp;

    /**
     * @var string
     */
    protected $twitterHashtags = null;

    /**
     * a cached array of twitter hashtags
     *
     * @var array
     */
    protected $twitterHashtags_ = null;

    public function injectObjectManager(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * an array of fields that if changed require a reindexing of all the events
     *
     * @return array
     */
    public static function getFieldsRequiringReindexing()
    {
        return self::$fieldsRequiringReindexing;
    }

    /**
     * Adds a Category
     *
     * @param Category $category The Category to be added
     * @return void
     */
    public function addCategory(Category $category)
    {
        if (!is_object($this->categories)) {
            $this->categories = $this->objectManager->get(ObjectStorage::class);
        }
        $this->categories->attach($category);
    }

    /**
     * generate a slug for this record
     */
    public function generateSlug()
    {
        // Only generate a new slug when none exists yet.
        $currentSlug = $this->getSlug();
        if ($currentSlug !== '') {
            return;
        }

        $value = $this->generateRawSlug();
        $value = Inflector::urlize($value);

        $eventRepository = $this->objectManager->get(EventRepository::class);
        $slug = $eventRepository->makeSlugUnique($value, $this->uid);
        $this->setSlug($slug);
    }

    /**
     * If a common location was set it will be returned. Otherwise
     * the inline location will be returned.
     *
     * @return Location
     */
    public function getActiveLocation()
    {
        $location = $this->getLocation();

        if (isset($location)) {
            return $location;
        }

        return $this->getLocationInline();
    }

    /**
     * If a common organizer was set it will be returned. Otherwise
     * the inline organizer will be returned.
     *
     * @return Organizer
     */
    public function getActiveOrganizer()
    {
        $organizer = $this->getOrganizer();

        if (isset($organizer)) {
            return $organizer;
        }

        return $this->getOrganizerInline();
    }

    /**
     * Getter for category
     *
     * @return ObjectStorage<Category> categories
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * getter for the first category
     *
     * @return Category
     */
    public function getCategory()
    {
        if (is_null($this->categories) || $this->categories->count() === 0) {
            return null;
        }
        $this->categories->rewind();
        return $this->categories->current();
    }

    /**
     * Getter for cruserFe
     *
     * @return int $cruserFe
     */
    public function getCruserFe()
    {
        return $this->cruserFe;
    }

    /**
     * Getter for description
     *
     * @return string a long description for this event
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return \DateTime
     */
    public function getEnableEndtime()
    {
        return $this->enableEndtime;
    }

    /**
     * Returns TRUE if an enable endtime is set and it is expired (meaning the event is not active any more).
     *
     * @return bool
     */
    public function getEnableEndtimeExpired()
    {
        $enableEndtime = $this->getEnableEndtime();
        if (!isset($enableEndtime)) {
            return false;
        }
        $enableEndtime = $enableEndtime->getTimestamp();
        if ($enableEndtime < 1) {
            return false;
        }
        return ($GLOBALS['EXEC_TIME'] > $enableEndtime);
    }

    /**
     * Getter for exceptions
     *
     * Extbase internal functionality can't be used here as
     * the records need to be fetched from two different tables
     *
     * @return ObjectStorage<Exception> exception
     */
    public function getExceptions()
    {
        if (isset($this->exceptionCache)) {
            return $this->exceptionCache;
        }

        $exceptionCache = $this->objectManager->get(ObjectStorage::class);

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
     * Returns all related file references.
     *
     * @return ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    public function getFileReferences()
    {
        return $this->files;
    }

    /**
     * get all files as an array
     *
     * @return File[]
     * @deprecated Use getFileReferences()
     */
    public function getFiles()
    {
        if (is_null($this->_cache_files)) {
            $this->_cache_files = FileArrayBuilder::buildFromReferences($this->files);
        }
        return $this->_cache_files;
    }

    /**
     * get the flickr tags as string
     *
     * @return string|false
     */
    public function getFlickrTags()
    {
        return $this->flickrTags;
    }

    /**
     * get an array of flickr tags
     *
     * @return array
     */
    public function getFlickrTagsArray()
    {
        if (is_null($this->flickrTags_)) {
            $this->buildFlickrTags();
        }
        return $this->flickrTags_;
    }

    /**
     * get a hash for this recurrance of the event
     *
     * @return string
     */
    public function getHash()
    {
        return md5(
            'event-' .
            $this->getUidLocalized() .
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']
        );
    }

    /**
     * Returns the image file references.
     *
     * @return ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    public function getImageReferences()
    {
        return $this->images;
    }

    /**
     * get all images as an array
     *
     * @return File[]
     * @deprecated Use getImageReferences()
     */
    public function getImages()
    {
        if (is_null($this->_cache_images)) {
            $this->_cache_images = FileArrayBuilder::buildFromReferences($this->images);
        }
        return $this->_cache_images;
    }

    /**
     * getter for lastIndexed
     *
     * @return \DateTime
     */
    public function getLastIndexed()
    {
        return $this->lastIndexed;
    }

    /**
     * getter for location
     *
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Getter for locationAddress
     *
     * @return string the address of the location this event takes place in
     * @deprecated Please get the property directly from the inline location.
     */
    public function getLocationAddress()
    {
        return $this->getLocationInline(true)->getAddress();
    }

    /**
     * Getter for locationCity
     *
     * @return string $locationCity
     * @deprecated Please get the property directly from the inline location.
     */
    public function getLocationCity()
    {
        return $this->getLocationInline(true)->getCity();
    }

    /**
     * Getter for locationCountry
     *
     * @return string $locationCountry
     * @deprecated Please get the property directly from the inline location.
     */
    public function getLocationCountry()
    {
        return $this->getLocationInline(true)->getCountry();
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
    public function getLocationInline($createDummyLocation = false, $persistDummyLocation = false)
    {
        $location = $this->locationInline;

        if (!isset($location) && $createDummyLocation) {
            /** @var Location $location */
            $location = $this->objectManager->get(Location::class);
            if ($persistDummyLocation) {
                $this->locationInline = $location;
            }
        }
        return $location;
    }

    /**
     * Getter for locationName
     *
     * @return string the name of the location this event takes place in
     * @deprecated Please get the property directly from the inline location.
     */
    public function getLocationName()
    {
        return $this->getLocationInline(true)->getName();
    }

    /**
     * Getter for locationZip
     *
     * @return string $locationZip
     * @deprecated Please get the property directly from the inline location.
     */
    public function getLocationZip()
    {
        return $this->getLocationInline(true)->getZip();
    }

    /**
     * get the next appointment of this event if any
     *
     * @return EventIndex
     */
    public function getNextAppointment()
    {
        $appointments = $this->getNextAppointments(1);
        return empty($appointments) ? null : $appointments->getFirst();
    }

    /**
     * get a list of next appointments
     *
     * @param $limit
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function getNextAppointments($limit = 3)
    {
        if (is_null($this->nextAppointments) || $this->nextAppointmentsCount < $limit) {
            $eventIndexRepository = $this->objectManager->get(EventIndexRepository::class);
            $this->nextAppointments = $eventIndexRepository->
            findNextAppointmentsByEventUid($this->getUid(), $limit);
            $this->nextAppointmentsCount = $limit;
        }
        if ($this->nextAppointmentsCount === $limit) {
            return $this->nextAppointments;
        } else {
            return array_slice($this->nextAppointments, 0, $limit);
        }
    }

    /**
     * get the organizer of the event
     *
     * @return Organizer
     */
    public function getOrganizer()
    {
        return $this->organizer;
    }

    /**
     * Getter for organizerAddress
     *
     * @return string $organizerAddress
     * @deprecated Please get the property directly from the inline organizer.
     */
    public function getOrganizerAddress()
    {
        return $this->getOrganizerInline(true)->getAddress();
    }

    /**
     * Getter for organizerCity
     *
     * @return string $organizerCity
     * @deprecated Please get the property directly from the inline organizer.
     */
    public function getOrganizerCity()
    {
        return $this->getOrganizerInline(true)->getCity();
    }

    /**
     * Getter for organizerCountry
     *
     * @return string $organizerCountry
     * @deprecated Please get the property directly from the inline organizer.
     */
    public function getOrganizerCountry()
    {
        return $this->getOrganizerInline(true)->getCountry();
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
    public function getOrganizerInline($createDummyOrganizer = false, $persistDummyOrganizer = false)
    {
        $organizer = $this->organizerInline;

        if (!isset($organizer) && $createDummyOrganizer) {
            /** @var Organizer $organizer */
            $organizer = $this->objectManager->get(Organizer::class);
            if ($persistDummyOrganizer) {
                $this->organizerInline = $organizer;
            }
        }
        return $organizer;
    }

    /**
     * Getter for organizerName
     *
     * @return string the name of the institution or person the event is organized by
     * @deprecated Please get the property directly from the inline organizer.
     */
    public function getOrganizerName()
    {
        return $this->getOrganizerInline(true)->getName();
    }

    /**
     * Getter for organizerZip
     *
     * @return string $organizerZip
     * @deprecated Please get the property directly from the inline organizer.
     */
    public function getOrganizerZip()
    {
        return $this->getOrganizerInline(true)->getZip();
    }

    /**
     * get all recurrances of this event
     *
     * @return TimelineEvent
     */
    public function getRecurrances()
    {
        $factory = new RecurranceFactory();
        return $factory->buildRecurranceForEvent($this);
    }

    /**
     * getter for showPageInstead
     *
     * @return string
     */
    public function getShowPageInstead()
    {
        return $this->showPageInstead;
    }

    /**
     * getter for slug
     *
     * @return string
     */
    public function getSlug()
    {
        return trim($this->slug);
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
    public function getStatus()
    {
        if (!isset($this->status)) {
            $this->status = $this->objectManager->get(EventStatus::class);
        }
        return (string)$this->status;
    }

    /**
     * Returns the sys_language_uid of this event.
     *
     * @return int
     */
    public function getSysLanguageUid()
    {
        return (int)$this->_languageUid;
    }

    /**
     * Getter for teaser
     *
     * @return string a short teaser for this event
     */
    public function getTeaser()
    {
        return $this->teaser;
    }

    /**
     * Getter for title
     *
     * @return string The title of this event
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *
     * @return \DateTime
     */
    public function getTstamp()
    {
        return $this->tstamp;
    }

    /**
     * get the twitterHashtags as string
     *
     * @return string
     */
    public function getTwitterHashtags()
    {
        return $this->twitterHashtags;
    }

    /**
     * get an array of twitter hashtags used for this event
     *
     * @return array
     */
    public function getTwitterHashtagsArray()
    {
        if (is_null($this->twitterHashtags_)) {
            $this->buildTwitterHashtags();
        }
        return $this->twitterHashtags_;
    }

    /**
     * Returns the localized UID of this event.
     *
     * @return int
     */
    public function getUidLocalized()
    {
        return (int)$this->_localizedUid;
    }

    /**
     * Returns the localized UID if it is available. Otherwise the current UID is returned.
     *
     * @return int
     */
    public function getUidLocalizedOrDefault()
    {
        $uidLocalized = (int)$this->getUidLocalized();
        if ($uidLocalized !== 0) {
            return $uidLocalized;
        } else {
            return $this->getUid();
        }
    }

    /**
     * @return boolean
     * @deprecated Use isEndTimePresent() instead.
     */
    public function hasEndTime()
    {
        return $this->isEndTimePresent();
    }

    /**
     * check if this is an allday event
     *
     * @return boolean
     */
    public function isAlldayEvent()
    {
        return $this->startTime === null;
    }

    /**
     * check if this record is enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return !$this->hidden && !$this->deleted && !$this->getEnableEndtimeExpired();
    }

    /**
     * check if this event has an endtime set
     *
     * @return boolean
     */
    public function isEndTimePresent()
    {
        return $this->endTime !== null;
    }

    /**
     * check if this event is on one day only
     *
     * @return boolean
     */
    public function isOneDayEvent()
    {
        return $this->endDay === null || $this->endDay === $this->startDay;
    }

    /**
     * returns true if this is a recurrant event
     *
     * @return boolean
     */
    public function isRecurrant()
    {
        return !empty($this->recurranceType) && $this->recurranceType !== 'none';
    }

    /**
     * Removes a Category
     *
     * @param Category $category The Category to be removed
     * @return void
     */
    public function removeCategory(Category $category)
    {
        $this->categories->detach($category);
    }

    public function resetSlug()
    {
        $this->slug = '';
    }

    /**
     * Setter for category
     *
     * @param ObjectStorage $categories
     * @return void
     */
    public function setCategories(ObjectStorage $categories = null)
    {
        $this->categories = $categories;
    }

    /**
     * Setter for cruserFe
     *
     * @param int $cruserFe
     * @return void
     */
    public function setCruserFe($cruserFe)
    {
        $this->cruserFe = $cruserFe;
    }

    /**
     * Setter for description
     *
     * @param string $description a long description for this event
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param \DateTime $enableEndtime
     */
    public function setEnableEndtime($enableEndtime)
    {
        $this->enableEndtime = $enableEndtime;
    }

    public function setFlickrTags($flickrTags)
    {
        $this->flickrTags = $flickrTags;
        $this->flickrTags_ = null;
    }

    /**
     * setter for lastIndexed
     *
     * @param \DateTime $lastIndexed
     * @return Event
     */
    public function setLastIndexed($lastIndexed)
    {
        $this->lastIndexed = $lastIndexed;
        return $this;
    }

    /**
     * setter for location
     *
     * @param Location $location
     * @return Event
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }

    /**
     * Setter for locationAddress
     *
     * @param string $locationAddress the address of the location this event takes place in
     * @return void
     * @deprecated Please set the property directly in the inline location.
     */
    public function setLocationAddress($locationAddress)
    {
        $this->getLocationInline(true, true)->setAddress($locationAddress);
    }

    /**
     * Setter for locationCity
     *
     * @param string $locationCity
     * @return void
     * @deprecated Please set the property directly in the inline location.
     */
    public function setLocationCity($locationCity)
    {
        $this->getLocationInline(true, true)->setCity($locationCity);
    }

    /**
     * Setter for locationCountry
     *
     * @param string $locationCountry
     * @return void
     * @deprecated Please set the property directly in the inline location.
     */
    public function setLocationCountry($locationCountry)
    {
        $this->getLocationInline(true, true)->setCountry($locationCountry);
    }

    /**
     * Setter for the inline location record.
     *
     * @param Location $locationInline
     */
    public function setLocationInline($locationInline)
    {
        $this->locationInline = $locationInline;
    }

    /**
     * Setter for locationName
     *
     * @param string $locationName the name of the location this event takes place in
     * @return void
     * @deprecated Please set the property directly in the inline location.
     */
    public function setLocationName($locationName)
    {
        $this->getLocationInline(true, true)->setName($locationName);
    }

    /**
     * Setter for locationZip
     *
     * @param string $locationZip
     * @return void
     * @deprecated Please set the property directly in the inline location.
     */
    public function setLocationZip($locationZip)
    {
        $this->getLocationInline(true, true)->setZip($locationZip);
    }

    /**
     * setter for organizer
     *
     * @param Organizer $organizer
     * @return Event
     */
    public function setOrganizer($organizer)
    {
        $this->organizer = $organizer;
        return $this;
    }

    /**
     * Setter for organizerAddress
     *
     * @param string $organizerAddress
     * @return void
     * @deprecated Please set the property directly in the inline organizer.
     */
    public function setOrganizerAddress($organizerAddress)
    {
        $this->getOrganizerInline(true, true)->setAddress($organizerAddress);
    }

    /**
     * Setter for organizerCity
     *
     * @param string $organizerCity
     * @return void
     * @deprecated Please set the property directly in the inline organizer.
     */
    public function setOrganizerCity($organizerCity)
    {
        $this->getOrganizerInline(true, true)->setCity($organizerCity);
    }

    /**
     * Setter for organizerCountry
     *
     * @param string $organizerCountry
     * @return void
     * @deprecated Please set the property directly in the inline organizer.
     */
    public function setOrganizerCountry($organizerCountry)
    {
        $this->getOrganizerInline(true, true)->setCountry($organizerCountry);
    }

    /**
     * Setter for the inline organizer record.
     *
     * @param Organizer $organizerInline
     */
    public function setOrganizerInline($organizerInline)
    {
        $this->organizerInline = $organizerInline;
    }

    /**
     * Setter for organizerName
     *
     * @param string $organizerName the name of the institution or person the event is organized by
     * @return void
     * @deprecated Please set the property directly in the inline organizer.
     */
    public function setOrganizerName($organizerName)
    {
        $this->getOrganizerInline(true, true)->setName($organizerName);
    }

    /**
     * Setter for organizerZip
     *
     * @param string $organizerZip
     * @return void
     * @deprecated Please set the property directly in the inline organizer.
     */
    public function setOrganizerZip($organizerZip)
    {
        $this->getOrganizerInline(true, true)->setZip($organizerZip);
    }

    /**
     * setter for showPageInstead
     *
     * @param string $showPageInstead
     * @return Event
     */
    public function setShowPageInstead($showPageInstead)
    {
        if (!empty($showPageInstead) && !is_numeric($showPageInstead) && strpos($showPageInstead, '://') === false) {
            $showPageInstead = 'http://' . $showPageInstead;
        }
        $this->showPageInstead = $showPageInstead;
        return $this;
    }

    /**
     * setter for slug
     *
     * @param string $slug
     * @return Event
     * @throws \InvalidArgumentException
     */
    public function setSlug($slug)
    {
        if (preg_match('/^[a-z0-9\-]*$/i', $slug) === false) {
            throw new \InvalidArgumentException(
                sprintf('"%s" is no valid slug. Only ASCII-letters, numbers and the hyphen are allowed.')
            );
        }
        $this->slug = $slug;
        return $this;
    }

    /**
     * Setter for teaser
     *
     * @param string $teaser a short teaser for this event
     * @return void
     */
    public function setTeaser($teaser)
    {
        $this->teaser = $teaser;
    }

    /**
     * Setter for title
     *
     * @param string $title The title of this event
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setTwitterHashtags($twitterHashtags)
    {
        $this->twitterHashtags = $twitterHashtags;
        $this->twitterHashtags_ = null;
    }

    /**
     * build the array of flickr tags
     */
    protected function buildFlickrTags()
    {
        $this->flickrTags_ = GeneralUtility::trimExplode(',', $this->flickrTags, true);
    }

    /**
     * build the array of twitter hashtags
     */
    protected function buildTwitterHashtags()
    {
        $this->twitterHashtags_ = GeneralUtility::trimExplode(',', $this->twitterHashtags, true);

        // Make sure each tag starts with a hash ("#")
        foreach ($this->twitterHashtags_ as &$hashtag) {
            if (strncmp($hashtag, '#', 1) !== 0) {
                $hashtag = '#' . $hashtag;
            }
        }
    }

    /**
     * generate a raw slug that might have invalid characters
     *
     * you could overwrite this if you want a different slug
     *
     * @return string
     */
    protected function generateRawSlug()
    {
        return $this->getTitle();
    }
}
