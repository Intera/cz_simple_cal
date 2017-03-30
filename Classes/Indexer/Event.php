<?php

namespace Tx\CzSimpleCal\Indexer;

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

use Tx\CzSimpleCal\Domain\Model\EventIndex;
use Tx\CzSimpleCal\Domain\Repository\EventIndexRepository;
use Tx\CzSimpleCal\Domain\Repository\EventRepository;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

/**
 * a class that handles indexing of events
 */
class Event {

	/**
	 * @var \Tx\CzSimpleCal\Domain\Repository\EventRepository
	 */
	protected $eventRepository;

	/**
	 * @var \Tx\CzSimpleCal\Domain\Repository\EventIndexRepository
	 */
	protected $eventIndexRepository;

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager;

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * A collection of all changed events during an index run.
	 * Only one event per storage page is registered.
	 * This information can be used for effective cache clearing.
	 * The array key is the page ID, the value is the event UID.
	 *
	 * @var array
	 */
	protected $processedEventIdsWithUniquePageIds = [];

	public function injectEventIndexRepository(EventIndexRepository $eventIndexRepository) {
		$this->eventIndexRepository = $eventIndexRepository;
	}

	public function injectEventRepository(EventRepository $eventRepository) {
		$this->eventRepository = $eventRepository;
	}

	public function injectObjectManager(ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}

	public function injectPersistenceManager(PersistenceManagerInterface $persistenceManager) {
		$this->persistenceManager = $persistenceManager;
	}

	/**
	 * create an eventIndex
	 *
	 * @param integer|\Tx\CzSimpleCal\Domain\Model\Event $event
	 */
	public function create($event) {
		if (is_integer($event)) {
			$event = $this->fetchEventObject($event);
		}

		$this->doCreate($event);
	}

	/**
	 * @return array
	 */
	public function getProcessedEventIdsWithUniquePageIds() {
		return $this->processedEventIdsWithUniquePageIds;
	}

	/**
	 * update an eventIndex
	 *
	 * @param integer|\Tx\CzSimpleCal\Domain\Model\Event $event
	 */
	public function update($event) {
		if (is_integer($event)) {
			$event = $this->fetchEventObject($event);
		}

		$this->doDelete($event);

		// We need to persist the deleted index entries, otherwise the event index SLUGs are invalid.
		$this->persistenceManager->persistAll();

		$this->doCreate($event);

	}

	/**
	 * delete the eventIndex
	 *
	 * @param integer|\Tx\CzSimpleCal\Domain\Model\Event $event
	 */
	public function delete($event) {
		if (is_integer($event)) {
			$event = $this->fetchEventObject($event);
		}

		$this->doDelete($event);
	}

	/**
	 * delete an event
	 *
	 * @param \Tx\CzSimpleCal\Domain\Model\Event $event
	 */
	protected function doDelete($event) {
		$this->registerProcessedEvent($event);
		$this->eventIndexRepository->removeAllNative($event->getUidLocalizedOrDefault());
	}

	/**
	 * create the indexes
	 *
	 * @param \Tx\CzSimpleCal\Domain\Model\Event $event
	 */
	protected function doCreate($event) {
		$this->registerProcessedEvent($event);
		$event->setLastIndexed(new \DateTime());
		$event->generateSlug();
		$this->eventRepository->update($event);

		if (!$event->isEnabled()) {
			return;
		}
		// get all recurrances...
		foreach ($event->getRecurrances() as $recurrance) {

			// ...and store them to the repository
			/** @var EventIndex $eventIndex */
			$eventIndex = $this->objectManager->get('Tx\\CzSimpleCal\\Domain\\Model\\EventIndex');
			$instance = EventIndex::fromArray(
				$eventIndex,
				$recurrance
			);

			$this->eventIndexRepository->add(
				$instance
			);
		}
	}

	/**
	 * get an event object by its uid
	 *
	 * @param integer $id
	 * @return \Tx\CzSimpleCal\Domain\Model\Event
	 * @throws \InvalidArgumentException
	 */
	protected function fetchEventObject($id) {
		$event = $this->eventRepository->findOneByUidEverywhere($id);
		if (empty($event)) {
			throw new \InvalidArgumentException(sprintf('An event with uid %d could not be found.', $id));
		}
		return $event;
	}

	/**
	 * @param \Tx\CzSimpleCal\Domain\Model\Event $event
	 */
	protected function registerProcessedEvent($event) {

		$pageId = $event->getPid();

		if (isset($this->processedEventIdsWithUniquePageIds[$pageId])) {
			return;
		}

		$this->processedEventIdsWithUniquePageIds[$pageId] = $event->getUid();
	}
}