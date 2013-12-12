<?php
namespace Tx\CzSimpleCal\Hook;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Christian Zenker <christian.zenker@599media.de>, 599media GmbH
 *
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
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;

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
	 * @var \Tx_CzSimpleCal_Indexer_Event
	 */
	protected $eventIndexer;

	/**
	 * @var \Tx_CzSimpleCal_Domain_Repository_EventRepository
	 */
	protected $eventRepository;

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
	 * Initializes all classes required for indexing events.
	 *
	 * @return void
	 */
	protected function initializeEventIndexClasses() {
		if (isset($this->eventIndexer)) {
			return;
		}
		/** @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager */
		$objectManager = GeneralUtility::makeInstance('Tx_Extbase_Object_ObjectManager');
		$this->eventIndexer = $objectManager->get('Tx_CzSimpleCal_Indexer_Event');
		$this->eventRepository = $objectManager->get('Tx_CzSimpleCal_Domain_Repository_EventRepository');
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
	}

	/**
	 * Implements the hook processCmdmap_postProcess.
	 *
	 * @param string $command
	 * @param string $table
	 * @param integer $id
	 * @param mixed $value
	 * @param \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler
	 * @return void
	 */
	public function processCmdmap_postProcess($command, $table, $id, $value, $dataHandler) {
		if ($table !== 'tx_czsimplecal_domain_model_event') {
			return;
		}

		switch ($command) {
			case 'move':
			case 'undelete':
				$this->registerUpdatedEvent($id);
				break;
			case 'delete':
				$this->registerUpdatedEvent($id, 'delete');
				break;
		}
	}

	/**
	 * implements the hook processDatamap_afterDatabaseOperations that gets invoked
	 * when a form in the backend was saved and written to the database.
	 *
	 * Here we will do the caching of recurring events
	 *
	 * @param string $status
	 * @param string $table
	 * @param integer $id
	 * @param array $fieldArray
	 * @param \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler
	 * @return void
	 */
	public function processDatamap_afterDatabaseOperations($status, $table, $id, $fieldArray, $dataHandler) {

		if ($table !== 'tx_czsimplecal_domain_model_event') {
			return;
		}

		if ($status === 'update') {
			$fieldsHaveChanged = $this->haveFieldsChanged(\Tx_CzSimpleCal_Domain_Model_Event::getFieldsRequiringReindexing(), $fieldArray);
			if (!$fieldsHaveChanged) {
				$this->addTranslatedFlashMessage('flashmessages.tx_czsimplecal_domain_model_event.updateNoIndex', \TYPO3\CMS\Core\Messaging\FlashMessage::INFO);
				return;
			}
		}

		if ($status === 'new') {
			$id = $dataHandler->substNEWwithIDs[$id];
		}

		$this->registerUpdatedEvent($id, $status);
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
	public function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, $dataHandler) {

		if ($table == 'tx_czsimplecal_domain_model_event' || $table == 'tx_czsimplecal_domain_model_exception') {
			// store the timezone to the database
			$fieldArray['timezone'] = date('T');
		}
	}

	/**
	 * Adds the given message to the flash message queue.
	 *
	 * @param string $translationKey
	 * @param integer $severity
	 * @return void
	 */
	protected function addTranslatedFlashMessage($translationKey, $severity = FlashMessage::OK) {
		$this->initializeFashMessageClasses();
		$message = $this->languageService->sl('LLL:' . $this->languageFile . ':' . $translationKey);
		/** @var FlashMessage $flashMessage */
		$flashMessage = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage', $message, '', $severity);
		$this->flashMessageService->getMessageQueueByIdentifier()->enqueue($flashMessage);
	}

	/**
	 * Get an event object by its uid.
	 *
	 * @param integer $id
	 * @throws \InvalidArgumentException
	 * @return \Tx_CzSimpleCal_Domain_Model_Event
	 */
	protected function fetchEventObject($id) {
		$event = $this->eventRepository->findOneByUidEverywhere($id);
		if (empty($event)) {
			throw new \InvalidArgumentException(sprintf('An event with uid %d could not be found.', $id));
		}
		return $event;
	}

	/**
	 * Generates the slug for the given event.
	 *
	 * @param \Tx_CzSimpleCal_Domain_Model_Event $event
	 * @return void
	 */
	protected function generateEventSlug($event) {
		$event->generateSlug();
		$this->eventRepository->update($event);
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
	 * Updated the index for the given event depending on the change type:
	 *
	 * new: Index will be updated and event slug will be generated.
	 * update: Index will be updated.
	 * delete: Index will be deleted.
	 *
	 * @param integer $eventUid The UID of the changed event.
	 * @param string $changeType The change type (new, update, delete)
	 * @return void
	 */
	protected function indexUpdatedEvent($eventUid, $changeType) {

		$event = $this->fetchEventObject($eventUid);

		switch ($changeType) {
			case 'update':
				$this->addTranslatedFlashMessage('flashmessages.tx_czsimplecal_domain_model_event.updateAndIndex');
				$this->eventIndexer->update($event);
				break;
			case 'new':
				$this->generateEventSlug($event);
				$this->addTranslatedFlashMessage('flashmessages.tx_czsimplecal_domain_model_event.create');
				$this->eventIndexer->update($event);
				break;
			case 'delete':
				$this->eventIndexer->delete($event);
				break;
		}
	}

	/**
	 * Loops over all changed events, updates the index entries and
	 * at the end persists the data in the databse.
	 *
	 * @return void
	 */
	protected function indexUpdatedEvents() {

		if (count($this->updatedEvents) === 0) {
			return;
		}

		$this->initializeEventIndexClasses();
		$updatedEvents = $this->updatedEvents;
		$this->updatedEvents = array();

		foreach ($updatedEvents as $eventUid => $changeType) {
			$this->indexUpdatedEvent($eventUid, $changeType);
		}

		$this->persistenceManager->persistAll();
	}

	/**
	 * Registers an updated event in the updatedEvents array.
	 *
	 * @param integer $eventUid The UID of the changed event.
	 * @param string $changeType The change type, can be new, update or delete.
	 * @return void
	 */
	protected function registerUpdatedEvent($eventUid, $changeType = 'update') {
		$this->updatedEvents[$eventUid] = $changeType;
	}
}