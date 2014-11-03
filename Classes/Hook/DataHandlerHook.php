<?php
namespace Tx\CzSimpleCal\Hook;

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

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This hook will be called by the TYPO3 DataHandler when a record
 * is created / changed / deleted in the Backend.
 *
 * It keeps the event index up to date.
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 * @author Alexander Stehlik <astehlik.deleteme@intera.de>
 */
class DataHandlerHook implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * @var \Tx\CzSimpleCal\Domain\Model\Event[]
	 */
	protected $eventCache = array();

	/**
	 * @var \Tx\CzSimpleCal\Indexer\Event
	 */
	protected $eventIndexer;

	/**
	 * @var \Tx\CzSimpleCal\Domain\Repository\EventRepository
	 */
	protected $eventRepository;

	/**
	 * This is set to TRUE if an event was updated in the Backend.
	 *
	 * @var bool
	 */
	protected $eventWasUpdated = FALSE;

	/**
	 * @var \TYPO3\CMS\Core\Messaging\FlashMessageService
	 */
	protected $flashMessageService;

	/**
	 * The language file that is used to translate the flash messages.
	 *
	 * @var string
	 */
	protected $languageFile = 'EXT:cz_simple_cal/Resources/Private/Language/locallang_mod.xml';

	/**
	 * @var \TYPO3\CMS\Lang\LanguageService
	 */
	protected $languageService;

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * Array containing all changed events. The changed event ID is
	 * used as index to prevent duplication and the value contains
	 * the change type which can be new, update or delete.
	 *
	 * @var array
	 */
	protected $updatedEvents = array();

	/**
	 * @var array
	 */
	protected $updatedExceptions = array();

	/**
	 * the extbase framework is not initialized in the constructor anymore
	 * because initializing the framework is costy
	 * and this class is *always* instanciated when *any* record
	 * is created or updated
	 *
	 * @return DataHandlerHook
	 */
	public function __construct() {
		/* don't do any extbasy stuff here! */
	}

	/**
	 * Will be called after all operations and process changed events.
	 *
	 * @param \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler
	 * @return void
	 */
	public function processCmdmap_afterFinish($dataHandler) {

		if (!$dataHandler->isOuterMostInstance()) {
			return;
		}

		$this->indexUpdatedEvents();

		// Make sure any dummy instances of TSFE are cleared.
		unset($GLOBALS['TSFE']);
	}

	/**
	 * Implements the hook processCmdmap_preProcess.
	 *
	 * @param string $command
	 * @param string $table
	 * @param int $id
	 * @param mixed $value
	 * @param \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler
	 * @param mixed $pasteUpdate
	 * @return void
	 */
	public function processCmdmap_preProcess(
		/** @noinspection PhpUnusedParameterInspection */
		$command, $table, $id, $value, $dataHandler, $pasteUpdate
	) {

		if ($table === 'tx_czsimplecal_domain_model_event') {

			switch ($command) {
				case 'move':
				case 'undelete':
					$this->registerUpdatedEvent($id, 'update');
					break;
				case 'delete':
					$this->registerUpdatedEvent($id, 'delete', TRUE);
					break;
			}

		} elseif ($table === 'tx_czsimplecal_domain_model_exception') {

			switch ($command) {
				case 'move':
				case 'undelete':
				case 'delete':
					$exception = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecord('tx_czsimplecal_domain_model_exception', $id);
					$this->registerUpdatedException($id, $exception);
					break;
			}
		}
	}

	/**
	 * We register new events after database operations because we need the id.
	 *
	 * @param string $status
	 * @param string $table
	 * @param integer $id
	 * @param array $fieldArray
	 * @param \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler
	 * @return void
	 */
	public function processDatamap_afterDatabaseOperations(
		$status,
		$table,
		$id,
		/** @noinspection PhpUnusedParameterInspection */
		$fieldArray,
		$dataHandler
	) {

		if ($status !== 'new') {
			return;
		}

		$id = $dataHandler->substNEWwithIDs[$id];

		if ($table === 'tx_czsimplecal_domain_model_event') {
			$this->registerUpdatedEvent($id, 'new');
		} elseif ($table === 'tx_czsimplecal_domain_model_exception') {
			$this->registerUpdatedException($id, NULL);
		}
	}

	/**
	 * Implement the hook processDatamap_postProcessFieldArray that gets invoked
	 * right before a dataset is written to the database
	 *
	 * @param string $status
	 * @param string $table
	 * @param integer $id
	 * @param array $fieldArray
	 * @param \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler
	 * @return void
	 */
	public function processDatamap_postProcessFieldArray(
		/** @noinspection PhpUnusedParameterInspection */
		$status, $table, $id, &$fieldArray, $dataHandler
	) {

		// If new exception or event is created initialize the timezone.
		if (
			$status === 'new'
			&& (
				$table == 'tx_czsimplecal_domain_model_event'
				|| $table == 'tx_czsimplecal_domain_model_exception'
			)
		) {
			$fieldArray['timezone'] = date('T');
		}

		if ($status !== 'update') {
			return;
		}

		if ($table === 'tx_czsimplecal_domain_model_event') {
			$this->eventWasUpdated = TRUE;
			if ($this->haveFieldsChanged(\Tx\CzSimpleCal\Domain\Model\Event::getFieldsRequiringReindexing(), $fieldArray)) {
				$this->registerUpdatedEvent($id, 'update');
			}
		} elseif ($table === 'tx_czsimplecal_domain_model_exception') {
			if (!empty($fieldArray)) {
				$this->registerUpdatedException($id, NULL);
			}
		}
	}

	/**
	 * Adds the given message to the flash message queue.
	 *
	 * @param string $message
	 * @param int $severity
	 */
	protected function addFlashmessage($message, $severity = FlashMessage::OK) {
		$this->initializeFashMessageClasses();
		/** @var FlashMessage $flashMessage */
		$flashMessage = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage', $message, '', $severity);
		$this->flashMessageService->getMessageQueueByIdentifier()->enqueue($flashMessage);
	}

	/**
	 * Fetches the translation for the given key and add the translated
	 * message to the flash message queue.
	 *
	 * @param string $translationKey
	 * @param integer $severity
	 * @return void
	 */
	protected function addTranslatedFlashMessage($translationKey, $severity = FlashMessage::OK) {
		static $displayedTranslationKeys;
		if (isset($displayedTranslationKeys[$translationKey])) {
			return;
		}
		$this->initializeFashMessageClasses();
		$message = $this->languageService->sl('LLL:' . $this->languageFile . ':' . $translationKey);
		$this->addFlashmessage($message, $severity);
		$displayedTranslationKeys[$translationKey] = TRUE;
	}

	/**
	 * Fetches the language of the given event from the database.
	 *
	 * @param int $uid
	 * @return int
	 */
	protected function fetchEventLanguage($uid) {
		$rows = $this->getDatabaseConnection()->exec_SELECTgetRows(
			'sys_language_uid',
			'tx_czsimplecal_domain_model_event',
			'deleted=0 AND uid=' . (int)$uid
		);
		if (empty($rows)) {
			throw new \RuntimeException(sprintf('sys_language_uid of Event with uid %d could not be determined.', $uid));
		}
		return (int)$rows[0]['sys_language_uid'];
	}

	/**
	 * Get an event object by its uid.
	 *
	 * @param int $id
	 * @param int $languageUid
	 * @throws \InvalidArgumentException
	 * @return \Tx\CzSimpleCal\Domain\Model\Event
	 */
	protected function fetchEventObject($id, $languageUid) {

		$id = (int)$id;
		$languageUid = (int)$languageUid;

		// Initialize a dummy TSFE with a configured sys_language_content
		// to fetch the correct language versions of the objects.
		// Will be cleared in processCmdmap_afterFinish().
		$GLOBALS['TSFE'] = new \stdClass();
		$GLOBALS['TSFE']->sys_language_content = $languageUid;
		$event = $this->eventRepository->findOneByUidEverywhere($id);

		// If language does not match, retry after clearing identity map.
		if (!empty($event) && $event->getSysLanguageUid() !== $languageUid) {
			$this->persistenceManager->clearState();
			$this->eventCache = array();
			$event = $this->eventRepository->findOneByUidEverywhere($id);
		}

		if (empty($event)) {
			throw new \InvalidArgumentException(sprintf('An event with uid %d could not be found.', $id));
		}

		if ($event->getUidLocalized() !== $id) {
			throw new \RuntimeException(sprintf('The UID of the event returned by the repository (%d) does not match the requested ID (%d).', $event->getUidLocalized(), $id));
		}

		return $event;
	}

	/**
	 * Fetches the UID of the event in the default language.
	 * If l18n_parent is 0 the current UID is returned, otherwise the parent UID is returned.
	 *
	 * @param int $uid
	 * @return int
	 */
	protected function fetchEventUidForDefaultLanguage($uid) {

		$uid = (int)$uid;
		$rows = $this->getDatabaseConnection()->exec_SELECTgetRows(
			'l18n_parent',
			'tx_czsimplecal_domain_model_event',
			'deleted=0 AND uid=' . $uid
		);

		if (empty($rows)) {
			throw new \RuntimeException(sprintf('l18n_parent of Event with uid %d could not be determined.', $uid));
		}

		$parentUid = (int)$rows[0]['l18n_parent'];
		if ($parentUid !== 0) {
			return $parentUid;
		} else {
			return $uid;
		}
	}

	/**
	 * Fetches all translations of the given UID.
	 * Returns an array containing the uid and sys_language_uid.
	 *
	 * @param $uid
	 * @return array
	 */
	protected function findEventTranslations($uid) {

		return (array)$this->getDatabaseConnection()->exec_SELECTgetRows(
			'uid,sys_language_uid',
			'tx_czsimplecal_domain_model_event',
			'deleted=0 AND l18n_parent=' . (int)$uid
		);
	}

	/**
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected function getDatabaseConnection() {
		return $GLOBALS['TYPO3_DB'];
	}

	/**
	 * Check if fields have been changed in the record.
	 *
	 * @param array $fields
	 * @param array $inArray
	 * @return boolean
	 */
	protected function haveFieldsChanged($fields, $inArray) {
		$criticalFields = array_intersect(
			array_keys($inArray),
			$fields
		);
		return !empty($criticalFields);
	}

	/**
	 * Indexes a single event (one language!).
	 * The event needs to be loaded to the event cache before this method is called.
	 *
	 * @param int $eventUid
	 * @param string $changeType
	 */
	protected function indexSingleEvent($eventUid, $changeType) {

		$event = $this->eventCache[$eventUid];

		switch ($changeType) {
			case 'update':
				$this->eventIndexer->update($event);
				$this->addTranslatedFlashMessage('flashmessages.tx_czsimplecal_domain_model_event.updateAndIndex');
				break;
			case 'new':
				$this->eventIndexer->update($event);
				$this->addTranslatedFlashMessage('flashmessages.tx_czsimplecal_domain_model_event.create');
				break;
			case 'delete':
				$this->eventIndexer->delete($event);
				break;
		}

		$this->persistenceManager->persistAll();
	}

	/**
	 * Updates the index for the given event in all languages depending on the change type:
	 *
	 * new: Index will be updated and event slug will be generated.
	 * update: Index will be updated and event slug will be generated if it is empty.
	 * delete: Index will be deleted.
	 *
	 * @param integer $eventUid The UID of the changed event.
	 * @param string $changeType The change type (new, update, delete)
	 * @return void
	 */
	protected function indexUpdatedEvent($eventUid, $changeType) {

		// Skip deleted events because they are processed directly.
		if ($changeType === 'delete') {
			return;
		}

		$eventUid = $this->fetchEventUidForDefaultLanguage($eventUid);

		$this->loadEventInCache($eventUid, 0);
		$this->indexSingleEvent($eventUid, $changeType);

		$translatedEvents = $this->findEventTranslations($eventUid);
		foreach ($translatedEvents as $translatedEvent) {
			$this->loadEventInCache($translatedEvent['uid'], $translatedEvent['sys_language_uid']);
			$this->indexSingleEvent($translatedEvent['uid'], $changeType);
		}
	}

	/**
	 * Loops over all changed events, updates the index entries and
	 * at the end persists the data in the databse.
	 *
	 * @return void
	 */
	protected function indexUpdatedEvents() {

		$this->loadUpdatedEventsFromExceptions();

		if (count($this->updatedEvents) === 0) {
			if ($this->eventWasUpdated) {
				$this->initializeFashMessageClasses();
				$this->addTranslatedFlashMessage('flashmessages.tx_czsimplecal_domain_model_event.updateNoIndex', \TYPO3\CMS\Core\Messaging\FlashMessage::INFO);
			}
			return;
		}

		$this->initializeEventIndexClasses();
		$updatedEvents = $this->updatedEvents;
		$this->updatedEvents = array();
		$errorDuringIndexing = FALSE;

		foreach ($updatedEvents as $eventUid => $changeType) {
			try {
				$this->indexUpdatedEvent($eventUid, $changeType);
			} catch (\UnexpectedValueException $e) {
				$errorDuringIndexing = TRUE;
				$translationKey = 'flashmessages.exception.indexUpdatedEvent';
				$exceptionCode = $e->getCode();
				if ($exceptionCode > 0) {
					$translationKey .= '.' . $exceptionCode;
				}
				$this->initializeFashMessageClasses();
				$message = $this->languageService->sl('LLL:' . $this->languageFile . ':' . $translationKey);
				$this->addFlashmessage(sprintf($message, $e->getMessage()), FlashMessage::ERROR);
			}
		}

		if (!$errorDuringIndexing) {
			$this->persistenceManager->persistAll();
		}
	}

	/**
	 * Initializes all classes required for indexing events.
	 *
	 * @return void
	 */
	protected function initializeEventIndexClasses() {
		if (isset($this->eventIndexer)) {
			return;
		}
		/** @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager */
		$objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		$this->eventIndexer = $objectManager->get('Tx\\CzSimpleCal\\Indexer\\Event');
		$this->eventRepository = $objectManager->get('Tx\\CzSimpleCal\\Domain\\Repository\\EventRepository');
		$this->persistenceManager = $objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\PersistenceManagerInterface');
	}

	/**
	 * Initializes all classes required for displaying flash messages.
	 *
	 * @return void
	 */
	protected function initializeFashMessageClasses() {
		if (isset($this->flashMessageService)) {
			return;
		}
		$this->flashMessageService = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessageService');
		$this->languageService = $GLOBALS['LANG'];
	}

	/**
	 * Loads the event with the given ID in the event cache so that it can
	 * be processed when the data handler has finished its work.
	 *
	 * @param int $eventId
	 * @param int $languageUid
	 */
	protected function loadEventInCache($eventId, $languageUid) {
		if (!isset($this->eventCache[$eventId])) {
			$this->initializeEventIndexClasses();
			$this->eventCache[$eventId] = $this->fetchEventObject($eventId, $languageUid);
		}
	}

	/**
	 * Loops over all registered Exceptions and loads the related events in the cache.
	 */
	protected function loadUpdatedEventsFromExceptions() {

		foreach ($this->updatedExceptions as $exceptionUid => $exception) {

			if (!isset($exception)) {
				$exception = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecord('tx_czsimplecal_domain_model_exception', $exceptionUid);
			}

			if (!is_array($exception)) {
				continue;
			}
			if ($exception['parent_table'] !== 'tx_czsimplecal_domain_model_event') {
				continue;
			}

			$this->registerUpdatedEvent($exception['parent_uid']);
		}
	}

	/**
	 * Registers an updated event in the updatedEvents array.
	 *
	 * @param integer $eventUid The UID of the changed event.
	 * @param mixed $changeType The change type, can be new, update or delete.
	 * @param bool $eventDeleted If TRUE the event will directly be removed
	 * from the Index because it will not be available any more after the
	 * DataHandler has finished processing.
	 * @return void
	 */
	protected function registerUpdatedEvent($eventUid, $changeType = FALSE, $eventDeleted = FALSE) {

		if (
			isset($this->updatedEvents[$eventUid])
			&& !isset($changeType)
		) {
			return;
		}

		if ($eventDeleted) {
			$languageUid = $this->fetchEventLanguage($eventUid);
			$this->loadEventInCache($eventUid, $languageUid);
			$this->indexSingleEvent($eventUid, 'delete');
		}

		$changeType = $changeType ?: 'update';
		$this->updatedEvents[$eventUid] = $changeType;
	}

	/**
	 * Registers the given uid as an updated exception.
	 *
	 * @param int $id
	 * @param array|null $exeptionData
	 */
	protected function registerUpdatedException($id, $exeptionData) {
		$this->updatedExceptions[$id] = $exeptionData;
	}
}