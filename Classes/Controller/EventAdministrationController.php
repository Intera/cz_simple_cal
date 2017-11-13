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

use Tx\CzSimpleCal\Utility\NullReturningDummyClass;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Controller for the Event object with editing capabilities for frontend-users
 *
 * (We use a seperate controller for this to avoid side effects with the
 *  BaseExtendableController)
 */
class EventAdministrationController extends ActionController
{
    /**
     * @var \Tx\CzSimpleCal\Domain\Repository\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var \Tx\CzSimpleCal\Domain\Repository\EventRepository
     */
    protected $eventRepository;

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * inject an categoryRepository
     *
     * @param \Tx\CzSimpleCal\Domain\Repository\CategoryRepository $categoryRepository
     */
    public function injectCategoryRepository(\Tx\CzSimpleCal\Domain\Repository\CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * inject an eventRepository
     *
     * @param \Tx\CzSimpleCal\Domain\Repository\EventRepository $eventRepository
     */
    public function injectEventRepository(\Tx\CzSimpleCal\Domain\Repository\EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * Creates a new event
     *
     * @param \Tx\CzSimpleCal\Domain\Model\Event $newEvent
     * @return void
     * @ignorevalidation $newEvent
     */
    public function createAction(\Tx\CzSimpleCal\Domain\Model\Event $newEvent)
    {
        $this->abortOnMissingUser();
        $this->setDefaults($newEvent);
        $newEvent->setCruserFe($this->getFrontendUserId());
        $this->view->assign('newEvent', $newEvent);

        if ($this->isEventValid($newEvent)) {
            $this->eventRepository->add($newEvent);

            // Persist event as the indexer needs an uid
            /** @var \TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface $persistenceManager */
            $persistenceManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\PersistenceManager');
            $persistenceManager->persistAll();
            // Create index for event
            /** @var \Tx\CzSimpleCal\Indexer\Event $eventIndexer */
            $eventIndexer = $this->getObjectManager()->get('Tx\\CzSimpleCal\\Indexer\\Event');
            $eventIndexer->create($newEvent);

            $this->addFlashMessage(
                sprintf('The event "%s" was created.', $newEvent->getTitle()),
                '',
                FlashMessage::OK
            );
            $this->clearCache();
            $this->logEventLifecycle($newEvent, 1);
            $this->redirect('list');
        }
    }

    /**
     * Deletes an existing event
     *
     * @param \Tx\CzSimpleCal\Domain\Model\Event $event The event to delete
     * @return void
     * @ignorevalidation $event
     */
    public function deleteAction(\Tx\CzSimpleCal\Domain\Model\Event $event)
    {
        $this->abortOnInvalidUser($event);

        // Delete index for event
        /** @var \Tx\CzSimpleCal\Indexer\Event $eventIndexer */
        $eventIndexer = $this->getObjectManager()->get('Tx\\CzSimpleCal\\Indexer\\Event');
        $eventIndexer->delete($event);

        $this->eventRepository->remove($event);
        $this->addFlashMessage(
            sprintf('The event "%s" was deleted.', $event->getTitle()),
            '',
            FlashMessage::OK
        );
        $this->clearCache();
        $this->logEventLifecycle($event, 3);
        $this->redirect('list');
    }

    /**
     * Displays a form for editing an existing event
     *
     * @param \Tx\CzSimpleCal\Domain\Model\Event $event
     * @return void
     * @ignorevalidation $event
     */
    public function editAction(\Tx\CzSimpleCal\Domain\Model\Event $event)
    {
        $this->abortOnInvalidUser($event);
        $categories = $this->getCategories();

        $this->view->assign('cats', $categories);
        $this->view->assign('event', $event);
    }

    /**
     * get the frontend User id
     */
    public function getFrontendUserId()
    {
        $fe_user = $GLOBALS['TSFE']->fe_user->user['uid'];
        return $fe_user ? $fe_user : false;
    }

    /**
     * get an instance of the objectManager
     *
     * Note:
     * =====
     * injecting the container using dependency injection
     * causes an error.
     *
     * @return \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    public function getObjectManager()
    {
        if (is_null($this->objectManager)) {
            $this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                'TYPO3\\CMS\\Extbase\\Object\\ObjectManager'
            );
        }
        return $this->objectManager;
    }

    /**
     * list all events by the logged in user
     *
     *
     */
    public function listAction()
    {
        $this->view->assign('events', $this->eventRepository->findAllByUserId($this->getFrontendUserId()));
    }

    /**
     * Displays a form for creating a new event
     *
     * @param \Tx\CzSimpleCal\Domain\Model\Event $fromEvent
     * @return void
     * @ignorevalidation $fromEvent
     */
    public function newAction(\Tx\CzSimpleCal\Domain\Model\Event $fromEvent = null)
    {
        $this->abortOnMissingUser();
        $event = new \Tx\CzSimpleCal\Domain\Model\Event();
        if ($fromEvent) {
            foreach ([
                         'categories',
                         'description',
                         'endDay',
                         'endTime',
                         'flickrTags',
                         'showPageInstead',
                         'startDay',
                         'startTime',
                         'teaser',
                         'title',
                         'twitterHashtags',
                     ] as $field) {
                $getter = 'get' . ucfirst($field);
                $setter = 'set' . ucfirst($field);
                $event->$setter($fromEvent->$getter());
            }

            if ($event) {
                $this->setDefaults($event);
                $event->setCruserFe($this->getFrontendUserId());
            }
        }

        // TODO: set organizer / location properties
        $categories = $this->getCategories();
        if (!$event->getCategory() && $categories->count() > 0) {
            /** @var \Tx\CzSimpleCal\Domain\Model\Category $category */
            $category = $categories->getFirst();
            $event->addCategory($category);
        }

        $this->view->assign('cats', $categories);
        $this->view->assign('newEvent', $event);
    }

    /**
     * set defaults on an object
     *
     * @param \Tx\CzSimpleCal\Domain\Model\Event $event
     */
    public function setDefaults($event)
    {
        $event->setTimezone(date('e'));
    }

    /**
     * Updates an existing event
     *
     * @param $event \Tx\CzSimpleCal\Domain\Model\Event
     * @return void
     * @ignorevalidation $event
     */
    public function updateAction(\Tx\CzSimpleCal\Domain\Model\Event $event)
    {
        $this->abortOnInvalidUser($event);
        $this->view->assign('event', $event);

        if ($this->isEventValid($event)) {
            $this->eventRepository->update($event);

            // Update index for event
            /** @var \Tx\CzSimpleCal\Indexer\Event $eventIndexer */
            $eventIndexer = $this->getObjectManager()->get('Tx\\CzSimpleCal\\Indexer\\Event');
            $eventIndexer->update($event);

            $this->addFlashMessage(
                sprintf('The event "%s" was updated.', $event->getTitle()),
                '',
                FlashMessage::OK
            );
            $this->clearCache();
            $this->logEventLifecycle($event, 2);
            $this->redirect('list');
        }
    }

    /**
     * abort the action if the user is invalid
     *
     * @param \Tx\CzSimpleCal\Domain\Model\Event $event The event
     */
    protected function abortOnInvalidUser($event)
    {
        if (!$event->getCruserFe() || $this->getFrontendUserId() == null
            || ($event->getCruserFe() != $this->getFrontendUserId())) {
            $this->throwStatus(403, 'You are not allowed to do this.');
        }
    }

    /**
     * abort the action if no user is logged in
     */
    protected function abortOnMissingUser()
    {
        if ($this->getFrontendUserId() <= 0) {
            $this->throwStatus(403, 'Please log in.');
        }
    }

    /**
     * clear the cache of the pages configured by the extension.
     *
     */
    protected function clearCache()
    {
        if (!$this->settings['clearCachePages']) {
            return;
        }

        $pids = $this->settings['clearCachePages'];
        $pids = GeneralUtility::trimExplode(',', $pids, true);

        if (empty($pids)) {
            return;
        }

        // Init TCEmain object
        /** @var \TYPO3\CMS\Core\DataHandling\DataHandler $tce */
        $tce = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\DataHandling\\DataHandler');
        if (!$tce->BE_USER) {
            /* that's a little ugly here:
             * We need some BE_USER as the cleanCache event will be logged to syslog.
             * We could use an empty "t3lib_beUserAuth", but this would flood the
             * syslog with entries of cleared caches.
             *
             * So we use this dummy class "\Tx\CzSimpleCal\Utility\Null" that just
             * ignores everything.
             */
            $tce->BE_USER = GeneralUtility::makeInstance(NullReturningDummyClass::class);
        }
        foreach ($pids as $pid) {
            $pid = intval($pid);
            if ($pid > 0) {
                $tce->clear_cacheCmd($pid);
            }
        }

        return;
    }

    /**
     * Gets the allowed Categories
     *
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     **/
    protected function getCategories()
    {
        return $this->categoryRepository->findAllByUids(
            GeneralUtility::intExplode(',', $this->settings['feEditableCategories'])
        );
    }

    /**
     * validate the event
     *
     * Considerations
     * ===============
     * Extbase Validation for models and properties is not suitable for most of the validations needed
     * as the validation would *always* be checked for the object - even if just displaying.
     * So if we don't want a frontend user to enter an event in the past and did it
     * using extbase's built-in validation, we would not be able to show *any* event
     * in the past.
     *
     * @param \Tx\CzSimpleCal\Domain\Model\Event $event
     * @return bool|array
     */
    protected function isEventValid($event)
    {
        $validator = $this->getObjectManager()->get('Tx\\CzSimpleCal\\Domain\\Validator\\UserEventValidator');

        if (!$validator->isValid($event)) {
            $this->request->setErrors($validator->getErrors());
            return false;
        }

        $cats = [];
        foreach (GeneralUtility::intExplode(',', $this->settings['feEditableCategories']) as $id) {
            $cats[] = intval($id);
        }

        /** @var \TYPO3\CMS\Extbase\Domain\Model\Category $cat */
        foreach ($event->getCategories() as $cat) {
            if (!in_array($cat->getUid(), $cats)) {
                return false;
            };
        }

        return true;
    }

    /**
     * log if an event was created/updated/deleted to make this transparent in the backend
     *
     * @param \Tx\CzSimpleCal\Domain\Model\Event $event
     * @param string $action the action (one of 1->'new', 2->'updated', 3->'delete')
     */
    protected function logEventLifecycle($event, $action)
    {
        /** @var \TYPO3\CMS\Core\Authentication\BackendUserAuthentication $user */
        $user = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Authentication\\BackendUserAuthentication');
        $actions = [
            1 => 'added',
            2 => 'edited',
            3 => 'deleted',
        ];
        $user->writelog(
            1,
            $action,
            0,
            0,
            'fe_user "%s" (%s) ' . $actions[$action] . ' the event "%s" (%s).',
            [
                $GLOBALS['TSFE']->fe_user->user['username'],
                $GLOBALS['TSFE']->fe_user->user['uid'],
                $event->getTitle(),
                $event->getUid(),
            ],
            '\Tx\CzSimpleCal\Domain\Model\Event',
            $event->getUid(),
            null,
            $event->getPid()
        );
    }
}
