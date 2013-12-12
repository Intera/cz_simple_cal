<?php

/**
 * a class that handles indexing of events
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class Tx_CzSimpleCal_Indexer_Event {

	static private
		$eventTable = 'tx_czsimplecal_domain_model_event',
		$eventIndexTable = 'tx_czsimplecal_domain_model_eventindex'
	;

	/**
	 * @var Tx_CzSimpleCal_Domain_Repository_EventRepository
	 * @inject
	 */
	protected $eventRepository = null;

	/**
	 * @var Tx_CzSimpleCal_Domain_Repository_EventIndexRepository
	 * @inject
	 */
	protected $eventIndexRepository = null;

	/**
	 * create an eventIndex
	 *
	 * @param integer|Tx_CzSimpleCal_Domain_Model_Event $event
	 */
	public function create($event) {
		if(is_integer($event)) {
			$event = $this->fetchEventObject($event);
		}

		$this->doCreate($event);
	}

	/**
	 * update an eventIndex
	 *
	 * @param integer|Tx_CzSimpleCal_Domain_Model_Event $event
	 */
	public function update($event) {
		if(is_integer($event)) {
			$event = $this->fetchEventObject($event);
		}

		$this->doDelete($event);
		$this->doCreate($event);

	}

	/**
	 * delete the eventIndex
	 *
	 * @param integer|Tx_CzSimpleCal_Domain_Model_Event $event
	 */
	public function delete($event) {
		if(is_integer($event)) {
			$event = $this->fetchEventObject($event);
		}

		$this->doDelete($event);
	}

	/**
	 * delete an event
	 *
	 * @param Tx_CzSimpleCal_Domain_Model_Event $event
	 */
	protected function doDelete($event) {
		$eventIndexEntries = $this->eventIndexRepository->findAllByEventEverywhere($event);
		foreach ($eventIndexEntries as $eventIndexEntry) {
			$this->eventIndexRepository->remove($eventIndexEntry);
		}
	}

	/**
	 * create the indexes
	 *
	 * @param Tx_CzSimpleCal_Domain_Model_Event $event
	 */
	protected function doCreate($event) {
		$event->setLastIndexed(new DateTime());
		$this->eventRepository->update($event);

		if(!$event->isEnabled()) {
			return;
		}
		// get all recurrances...
		foreach($event->getRecurrances() as $recurrance) {
			// ...and store them to the repository
			$instance = Tx_CzSimpleCal_Domain_Model_EventIndex::fromArray(
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
	 * @throws InvalidArgumentException
	 * @return Tx_CzSimpleCal_Domain_Model_Event
	 */
	protected function fetchEventObject($id) {
		$event = $this->eventRepository->findOneByUidEverywhere($id);
		if(empty($event)) {
			throw new InvalidArgumentException(sprintf('An event with uid %d could not be found.', $id));
		}
		return $event;
	}
}