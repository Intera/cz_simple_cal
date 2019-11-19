<?php
declare(strict_types=1);

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

use DateTime;
use Tx\CzSimpleCal\Domain\Model\Category;
use Tx\CzSimpleCal\Domain\Model\EventIndex;
use Tx\CzSimpleCal\Domain\Repository\CategoryRepository;
use Tx\CzSimpleCal\Domain\Repository\EventIndexRepository;
use Tx\CzSimpleCal\Utility\DateTime as CzSimpleCalDateTime;
use Tx\CzSimpleCal\Utility\YearOptionCollector;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Service\ExtensionService;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Controller for the EventIndex object
 */
class EventIndexController extends BaseExtendableController
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var EventIndexRepository
     */
    protected $eventIndexRepository;

    /**
     * @var ExtensionService
     */
    protected $extensionService;

    /**
     * @var YearOptionCollector
     */
    protected $yearOptionCollector;

    public function injectCategoryRepository(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function injectEventIndexRepository(EventIndexRepository $eventIndexRepository)
    {
        $this->eventIndexRepository = $eventIndexRepository;
    }

    public function injectExtensionService(ExtensionService $extensionService)
    {
        $this->extensionService = $extensionService;
    }

    public function injectYearOptionCollector(YearOptionCollector $yearOptionCollector)
    {
        $this->yearOptionCollector = $yearOptionCollector;
    }

    /**
     * count events and group them by an according timespan
     */
    public function countEventsAction()
    {
        $start = $this->getStartDate();
        $end = $this->getEndDate();

        $this->view->assign('start', $start);
        $this->view->assign('end', $end);

        $this->view->assign(
            'data',
            $this->eventIndexRepository->countAllWithSettings(
                array_merge(
                    $this->actionSettings,
                    [
                        'startDate' => $start->getTimestamp(),
                        'endDate' => $end->getTimestamp(),
                    ]
                )
            )
        );

        $this->view->assign('categories', $this->categoryRepository->findAll());
    }

    /**
     * builds a list of some events
     */
    public function listAction()
    {
        $start = $this->getStartDate();
        $end = $this->getEndDate();

        $this->view->assign('start', $start);
        $this->view->assign('end', $end);

        $filterSetting = [];
        if ($start) {
            $filterSetting['startDate'] = $start->getTimestamp();
        }
        if ($end) {
            $filterSetting['endDate'] = $end->getTimestamp();
        }

        $this->view->assign(
            'events',
            $this->eventIndexRepository->findAllWithSettings(
                array_merge(
                    $this->actionSettings,
                    $filterSetting
                )
            )
        );

        $this->view->assign('categories', $this->categoryRepository->findAll());
        $this->view->assign('selectedCategory', $this->getSelectedCategory());

        $this->view->assign('years', $this->yearOptionCollector->buildYearOptions());
        $this->view->assign('selectedYear', $this->actionSettings['filter']['year'] ?? '');

        $this->view->assign('gridColumnClasses', $this->getGridSetting('gridColumnClassMapping'));
        $this->view->assign('gridWidth', $this->getGridSetting('gridWidthMapping'));
    }

    /**
     * Render calendar events with the newest timestamps as RSS feed.
     */
    public function rssLatestAction()
    {
        $events = $this->eventIndexRepository->findLatest($this->actionSettings['maxEvents']);
        $this->initializeRssVariablesInView('latest');
        $this->view->assign('events', $events);
    }

    /**
     * Render upcoming events as RSS feed.
     */
    public function rssUpcomingAction()
    {
        $events = $this->eventIndexRepository->findAllWithSettings($this->actionSettings);
        $this->initializeRssVariablesInView('upcoming');
        $this->view->assign('events', $events);
    }

    /**
     * display a single event
     *
     * @param integer $event
     */
    public function showAction($event)
    {
        /* don't let Extbase fetch the event
         * as you won't be able to extend the model
         * via an extension
         */
        /** @var EventIndex $eventIndexObject */
        $eventIndexObject = $this->eventIndexRepository->findByUid($event);

        if (empty($eventIndexObject)) {
            $this->throwStatus(404, null, $this->translateById('error-404-event-index-not-found'));
        }

        $this->view->assign('event', $eventIndexObject);
    }

    /**
     * Builds the title for the RSS feed for the given type.
     *
     * @param string $type
     * @return string
     */
    protected function buildRssTitle($type)
    {
        $pageTitleSuffix = ' - ' . $this->getTypoScriptFrontendController()->generatePageTitle();

        if (!empty($this->settings['rss'][$type]['channelTitle'])) {
            return $this->settings['rss'][$type]['channelTitle'];
        }

        $contentTitle = $this->configurationManager->getContentObject()->data['header'];
        $rssTypeTitle = ' (' . $this->translateById('rss_title_suffix_' . $type) . ')';
        return $contentTitle . $rssTypeTitle . $pageTitleSuffix;
    }

    /**
     * get the end date of events that should be fetched
     *
     * @return CzSimpleCalDateTime
     * @todo getDate support
     */
    protected function getEndDate()
    {
        if (array_key_exists('endDate', $this->actionSettings)) {
            if (isset($this->actionSettings['getDate'])) {
                $date = new CzSimpleCalDateTime($this->actionSettings['getDate']);
                $date->modify($this->actionSettings['endDate']);
            } else {
                $date = new CzSimpleCalDateTime($this->actionSettings['endDate']);
            }
            return $date;
        } else {
            return null;
        }
    }

    protected function getGridSetting($mappingConfigKey)
    {
        $gridWithSetting = $this->settings['gridWidth'] ?? '';
        $settingMapping = $this->actionSettings[$mappingConfigKey];

        $settingValue = $settingMapping[$gridWithSetting] ?? '';
        if (!$settingValue) {
            $settingValue = array_shift($settingMapping);
        }

        return $settingValue;
    }

    protected function getSelectedCategory(): ?Category
    {
        if (empty($this->actionSettings['filter']['categories.uid'])) {
            return null;
        }

        $filterCategories = GeneralUtility::intExplode(
            ',',
            $this->actionSettings['filter']['categories.uid'],
            true
        );
        if ($filterCategories == []) {
            return null;
        }

        return $this->categoryRepository->findByUid($filterCategories[0]);
    }

    /**
     * get the start date of events that should be fetched
     *
     * @return CzSimpleCalDateTime
     */
    protected function getStartDate()
    {
        if (array_key_exists('startDate', $this->actionSettings)) {
            if (isset($this->actionSettings['getDate'])) {
                $date = new CzSimpleCalDateTime($this->actionSettings['getDate']);
                $date->modify($this->actionSettings['startDate']);
            } else {
                $date = new CzSimpleCalDateTime($this->actionSettings['startDate']);
            }
            return $date;
        } else {
            return null;
        }
    }

    protected function getTypoScriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }

    /**
     * Loads some variables in the current view that are required by all RSS feeds.
     *
     * @param string $feedType "latest" or "upcoming"
     * @return void
     */
    protected function initializeRssVariablesInView($feedType)
    {
        $this->view->assign(
            'feedSettings',
            isset($this->settings['rss'][$feedType]) ? $this->settings['rss'][$feedType] : []
        );
        $this->view->assign('language', $this->getTypoScriptFrontendController()->lang);
        $this->view->assign('currentUrl', GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL'));
        $this->view->assign('channelTitle', $this->buildRssTitle($feedType));
        $this->view->assign('channelLastBuildDate', new DateTime());
    }

    /**
     * Initializes some common variables in the view.
     *
     * The current content object data is loaded and the RSS feed titles.
     *
     * @param ViewInterface $view
     * @return void
     */
    protected function initializeView(ViewInterface $view)
    {
        $this->view->assign('contentObjectData', $this->configurationManager->getContentObject()->data);
        $this->view->assign('rssTitleLatest', $this->buildRssTitle('latest'));
        $this->view->assign('rssTitleUpcoming', $this->buildRssTitle('upcoming'));
        $this->view->assign(
            'defaultPid',
            $this->extensionService->getTargetPidByPlugin(
                $this->request->getControllerExtensionName(),
                $this->request->getPluginName()
            )
        );
    }

    /**
     * Returns the translation for the given key from the cz_simple_cal Extension.
     *
     * @param string $key
     * @param string $extensionName
     * @return string
     */
    protected function translateById($key, $extensionName = 'CzSimpleCal')
    {
        return LocalizationUtility::translate($key, $extensionName);
    }
}
