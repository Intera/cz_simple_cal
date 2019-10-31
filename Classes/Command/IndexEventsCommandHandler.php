<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Command;

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

use Doctrine\DBAL\FetchMode;
use Exception;
use RuntimeException;
use Tx\CzSimpleCal\Domain\Repository\EventRepository;
use Tx\CzSimpleCal\Indexer\Event;
use Tx\CzSimpleCal\Indexer\Event as EventIndexer;
use Tx\CzSimpleCal\Utility\StrToTime;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

/**
 * the scheduler task to index all events
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class IndexEventsCommandHandler
{
    /**
     * a factor that determines how carefull
     * the decision if to run another loop should be made
     *
     * the higher the value, the earlier the script will abort
     *
     * @var float
     */
    const FACTOR_FOR_LOOP_DETERMINATION = 1.5;

    /**
     * event will be reindexed if the last indexing is older than that
     *
     * will be parsed through \Tx\CzSimpleCal\Utility\StrToTime
     *
     * @var string
     */
    public $minIndexAge;

    /**
     * the number of records to fetch at a time before persisting
     *
     * (might be configurable later on)
     *
     * @var integer
     */
    protected $chunkSize = 50;

    /**
     * the time where the script is thought to end
     *
     * this is very, very vague and usually more strict then reality
     * for example calls to the database are not taken into account by PHP
     *
     * @var float
     */
    protected $endOfScriptTime = null;

    /**
     * @var EventRepository
     */
    protected $eventRepository = null;

    /**
     * @var EventIndexer
     */
    protected $indexer = null;

    /**
     * memory consumption before starting the last loop
     *
     * @var int
     */
    protected $lastMemory = null;

    /**
     * microtime of the start of the last loop
     *
     * @var float
     */
    protected $lastStart = null;

    /**
     * the maximum execution time used for indexing one previous chunk
     *
     * @var float
     */
    protected $maxChunkDuration = null;

    /**
     * the maximum added memory usage used for one previous chunk
     *
     * @var integer
     */
    protected $maxChunkMemoryIncrease = null;

    /**
     * the max_execution_time of PHP in seconds
     *
     * this is used to guess if an other cycle would run into this limit and abort earlier
     *
     * @var integer
     */
    protected $maxExecutionTime = null;

    /**
     * the memory_limit of PHP in bytes
     *
     * this is used to guess if an other cycle would run into this limit and abort earlier
     *
     * @var integer
     */
    protected $memoryLimit = null;

    protected $minIndexAgeAbsolute = null;

    /**
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager = null;

    /**
     * execute this task
     *
     * @param string $minIndexAge
     */
    public function execute(string $minIndexAge)
    {
        $this->minIndexAge = $minIndexAge;

        $this->init();
        $eventsFound = false;

        while ($this->shouldAnotherChunkBeProcessed()) {
            $recordsProcessed = false;

            foreach ($this->getLanguages() as $language) {
                $this->persistenceManager->clearState();
                $_GET['L'] = $language['uid'];
                $events = $this->eventRepository->findRecordsForReindexing(
                    $this->chunkSize,
                    $this->minIndexAgeAbsolute
                );
                if (!$events->count() > 0) {
                    continue;
                }
                $eventsFound = true;
                $recordsProcessed = true;
                $this->indexEvents($events);
                $this->persistenceManager->persistAll();
            }

            // If no records were processed for any language we stop the loop.
            if (!$recordsProcessed) {
                break;
            }
        }

        if (!$eventsFound) {
            return;
        }

        $this->clearCacheForProcessedEvents();
    }

    /**
     * Uses the cacheopt Extension (if loaded) to clear all related caches for the processed events.
     */
    protected function clearCacheForProcessedEvents()
    {
        if (!ExtensionManagementUtility::isLoaded('cacheopt')) {
            return;
        }

        /** @noinspection PhpFullyQualifiedNameUsageInspection */
        /** @var \Tx\Cacheopt\CacheApi $cacheApi */
        $cacheApi = GeneralUtility::makeInstance('Tx\\Cacheopt\\CacheApi');
        foreach ($this->indexer->getProcessedEventIdsWithUniquePageIds() as $eventUid) {
            $cacheApi->flushCacheForRecordWithDataHandler('tx_czsimplecal_domain_model_event', $eventUid);
        }
    }

    /**
     * @return LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

    /**
     * Returns an array containing rows with uids of all available languages including default (0).
     *
     * @return array
     */
    protected function getLanguages()
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $builder = $connectionPool->getQueryBuilderForTable('sys_language');
        $builder->from('sys_language')->select('uid');
        $configuredLanguages = $builder->execute()->fetchAll(FetchMode::ASSOCIATIVE);
        return array_merge([['uid' => 0]], $configuredLanguages);
    }

    /**
     * one (of likely many) loops of processing a given
     * chunk of events
     *
     * @param $events
     */
    protected function indexEvents($events)
    {
        foreach ($events as $event) {
            $this->indexer->update($event);
        }
    }

    /**
     * init some needed objects and variables
     */
    protected function init()
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->eventRepository = $objectManager->get(EventRepository::class);
        $this->indexer = $objectManager->get(EventIndexer::class);
        $this->persistenceManager = $objectManager->get(PersistenceManager::class);

        try {
            $this->maxExecutionTime = intval(ini_get('max_execution_time'));
        } catch (Exception $e) {
        }
        if (!$this->maxExecutionTime || $this->maxExecutionTime < 5) {
            // If value could not be determined or it seems faulty
            $this->maxExecutionTime = 30;
        }

        try {
            $memoryLimit = ini_get('memory_limit');
            $this->memoryLimit = GeneralUtility::getBytesFromSizeMeasurement($memoryLimit);
        } catch (Exception $e) {
        }
        if (!$this->memoryLimit || $this->memoryLimit < 0x2000000) {
            // If value could not be determined or it seems faulty
            $this->memoryLimit = 0x2000000; // =32M
        }

        $this->initMinIndexAge();
    }

    /**
     * Validates the additional fields' values
     */
    protected function initMinIndexAge(): void
    {
        if ($this->minIndexAge === null) {
            $this->minIndexAgeAbsolute = null;
            return;
        }

        $minIndexAge = StrToTime::strtotime($this->minIndexAge);
        if ($minIndexAge === false) {
            throw new RuntimeException(
                sprintf(
                    $this->localize('tx_czsimplecal_scheduler_index.minindexage.parseerror'),
                    $this->minIndexAge
                )
            );
        }

        $this->minIndexAgeAbsolute = $minIndexAge;
    }

    protected function localize(string $key): string
    {
        return $this->getLanguageService()->sL($this->getLocallangPrefix() . $key);
    }

    /**
     * the logic to determine if another loop of indexing
     * a chunk of events should be done or not
     *
     * This logic was added so that the script won't run out of time or memory
     * and remain in an uncertain state.
     * Having a fixed number of events to process did not seem to be a good solution
     * as events with lots of recurrances are usually more time consuming than
     * simple events without recurrance.
     *
     * @return boolean
     */
    protected function shouldAnotherChunkBeProcessed()
    {
        if (is_null($this->lastStart) || is_null($this->lastMemory)) {
            // If: this is the first loop -> init some values
            $this->lastStart = microtime(true);
            $this->lastMemory = memory_get_peak_usage();

            $this->endOfScriptTime = $this->lastStart - 1 + $this->maxExecutionTime;
            // Always do at least one loop
            return true;
        }

        $microtime = microtime(true);
        $memory_get_peak_usage = memory_get_peak_usage();

        // Update the max* values if they have changed
        $duration = $microtime - $this->lastStart;
        if ($duration > $this->maxChunkDuration) {
            $this->maxChunkDuration = $duration;
        }
        $memoryIncrease = $memory_get_peak_usage - $this->lastMemory;
        if ($memoryIncrease > $this->maxChunkMemoryIncrease) {
            $this->maxChunkMemoryIncrease = $memoryIncrease;
        }

        // Check if another loop should be done
        if ($this->endOfScriptTime < $microtime + self::FACTOR_FOR_LOOP_DETERMINATION * $this->maxChunkDuration) {
            // If: the script might take too long
            return false;
        }

        if ($this->hasMemoryLimitBeenReached($memory_get_peak_usage)) {
            // If: memory usage might explode
            return false;
        }

        $this->lastStart = $microtime;
        $this->lastMemory = $memory_get_peak_usage;
        return true;
    }

    private function getLocallangPrefix(): string
    {
        return 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_mod.xml:';
    }

    /**
     * @param $memoryPeakUsage
     * @return bool
     */
    private function hasMemoryLimitBeenReached($memoryPeakUsage): bool
    {
        $peakUsageWithBuffer = $memoryPeakUsage + self::FACTOR_FOR_LOOP_DETERMINATION * $this->maxChunkMemoryIncrease;
        return $this->memoryLimit < $peakUsageWithBuffer;
    }
}
