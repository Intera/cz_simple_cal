<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Domain\Repository;

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

use DateTimeZone;
use InvalidArgumentException;
use PDO;
use Tx\CzSimpleCal\Domain\Model\Event;
use Tx\CzSimpleCal\Domain\Model\EventIndex;
use Tx\CzSimpleCal\Utility\DateTime;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Repository for EventIndex domain models.
 *
 * @method EventIndex findByUid($uid)
 */
class EventIndexRepository extends Repository
{
    /**
     * filter settings for all allowed properties for setupSettings()
     *
     * @var array
     */
    protected static $filterSettings = [
        'startDate' => [
            'filter' => FILTER_VALIDATE_INT,
            'options' => [
                'min_range' => 0,
                'default' => null,
            ],
        ],
        'endDate' => [
            'filter' => FILTER_VALIDATE_INT,
            'options' => [
                'min_range' => 0,
                'default' => null,
            ],
        ],
        'maxEvents' => [
            'filter' => FILTER_VALIDATE_INT,
            'options' => [
                'min_range' => 1,
                'default' => null,
            ],
        ],
        'order' => [
            'filter' => FILTER_CALLBACK,
            'options' => [
                __CLASS__,
                'sanitizeOrder',
            ],
        ],
        'orderBy' => [
            'filter' => FILTER_CALLBACK,
            'options' => [
                __CLASS__,
                'sanitizeString',
            ],
        ],
        'groupBy' => [
            'filter' => FILTER_CALLBACK,
            'options' => [
                __CLASS__,
                'sanitizeString',
            ],
        ],
        'includeStartedEvents' => ['filter' => FILTER_VALIDATE_BOOLEAN],
        'excludeOverlongEvents' => ['filter' => FILTER_VALIDATE_BOOLEAN],
        'filter' => [
            'filter' => FILTER_UNSAFE_RAW, // This is treated seperately
            'flags' => FILTER_FORCE_ARRAY,
        ],
    ];

    /**
     * sanitizing the value for the "filter" setting.
     * If multiple values are given return an array
     *
     * @param $filter
     * @return array
     */
    protected static function sanitizeFilter($filter)
    {
        if (!is_array($filter)) {
            $filter = [
                'value' => GeneralUtility::trimExplode(',', $filter, true),
            ];
        } elseif (!empty($filter['_typoScriptNodeValue']) && !is_array($filter['_typoScriptNodeValue'])) {
            /* this field is set if something like
             *     filter {
             *         foo = bar
             *         foo.negate = 1
             *     }
             * was set in the frontend
             *
             * This is processed prior to the value field, so
             * that a flexform is able to override it.
             */
            $filter['value'] = GeneralUtility::trimExplode(',', $filter['_typoScriptNodeValue'], true);
            unset($filter['_typoScriptNodeValue']);
        } elseif (!empty($filter['value']) && !is_array($filter['value'])) {
            $filter['value'] = GeneralUtility::trimExplode(',', $filter['value'], true);
        }

        foreach ($filter['value'] as &$value) {
            if (is_numeric($value)) {
                $value = intval($value);
            }
        }
        return empty($filter['value']) ? null : $filter;
    }

    /**
     * sanitize the "order" setting
     *
     * @param $value
     * @return string|null
     */
    protected static function sanitizeOrder($value)
    {
        if (!is_string($value)) {
            return null;
        }
        $value = strtolower($value);
        if ($value === 'desc') {
            return 'desc';
        } elseif ($value === 'asc') {
            return 'asc';
        }
        return null;
    }

    /**
     * sanitize something to be a valid string
     * (only ASCII letters, numbers, ".", "_" and "-")
     *
     * @param $value
     * @return string
     */
    protected static function sanitizeString($value)
    {
        $value = trim($value);
        if (!is_string($value) || empty($value)) {
            return null;
        }
        if (preg_match('/[^a-z0-9._\-]/i', trim($value))) {
            // If: there is anything not a letter, number, dot, underscore or hyphen
            return null;
        } else {
            return $value;
        }
    }

    /**
     * call an event before adding an event to the repo
     *
     * @param $object
     */
    public function add($object)
    {
        $object->preCreate();
        parent::add($object);
    }

    /**
     * find all events matching some settings and count them
     *
     * for all options for the settings see setupSettings()
     *
     * @param $settings
     * @return array
     * @see setupSettings()
     * @see doCountAllWithSettings()
     */
    public function countAllWithSettings($settings = [])
    {
        $settings = $this->cleanSettings($settings);
        return $this->doCountAllWithSettings($settings);
    }

    /**
     * find all records and return them ordered by the start date ascending
     *
     * @return array
     */
    public function findAll()
    {
        $query = $this->createQuery();
        $query->setOrderings(['start' => 'ASC']);
        return $query->execute();
    }

    /**
     * Finds all event index entries for the given event without
     * respecting storage pages or enable fields.
     *
     * @param Event $event
     * @return QueryResultInterface
     */
    public function findAllByEventEverywhere($event)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()
            ->setRespectStoragePage(false)
            ->setRespectSysLanguage(false)
            ->setIgnoreEnableFields(true);
        $query->matching(
            $query->equals('event', $event->getUidLocalized())
        );
        return $query->execute();
    }

    /**
     * find all events matching some settings
     *
     * for all options for the settings see setupSettings()
     *
     * @param $settings
     * @return QueryResultInterface
     * @see setupSettings()
     */
    public function findAllWithSettings($settings = [])
    {
        $settings = $this->cleanSettings($settings);
        $query = $this->setupSettings($settings);

        return $query->execute();
    }

    /**
     * Finds all events ordered by the event timestamp so that events that were changed most recently are returned
     * first.
     *
     * @param int $maxEvents
     * @return array|QueryResultInterface
     */
    public function findLatest($maxEvents)
    {
        $query = $this->createQuery();
        $query->setOrderings(['event.tstamp' => QueryInterface::ORDER_DESCENDING]);
        if ($maxEvents > 0) {
            $query->setLimit((int)$maxEvents);
        }
        return $query->execute();
    }

    /**
     * get a list of upcomming appointments by an event uid
     *
     * @param integer $eventUid
     * @param integer $limit
     * @return array|QueryResultInterface
     */
    public function findNextAppointmentsByEventUid($eventUid, $limit = 3)
    {
        $query = $this->createQuery();
        $query->setLimit($limit);
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $query->matching(
            $query->logicalAnd(
                $query->equals('event.uid', $eventUid),
                $query->greaterThanOrEqual('start', time())
            )
        );
        $query->setOrderings(['start' => QueryInterface::ORDER_ASCENDING]);

        return $query->execute();
    }

    /**
     * make a given slug unique among all records
     *
     * @param $slug
     * @param $uid
     * @return string the unique slug
     */
    public function makeSlugUnique($slug, $uid)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()
            ->setRespectStoragePage(false)
            ->setIgnoreEnableFields(true)
            ->setRespectSysLanguage(true);

        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $query->matching(
            $query->logicalAnd(
                $query->equals('slug', $slug),
                $query->logicalNot($query->equals('uid', $uid))
            )
        );
        $count = $query->count();
        if ($count !== false && $count == 0) {
            return $slug;
        } else {
            $query = $this->createQuery();
            $query->getQuerySettings()->
            setRespectStoragePage(false)->
            setIgnoreEnableFields(true)->
            setRespectSysLanguage(false);
            /** @noinspection PhpMethodParametersCountMismatchInspection */
            $query->matching(
                $query->logicalAnd(
                    $query->like('slug', $slug . '-%'),
                    $query->logicalNot($query->equals('uid', $uid))
                )
            );
            $query->setOrderings(
                [
                    'slug' => QueryInterface::ORDER_DESCENDING,
                ]
            );
            $query->setLimit(1);
            $result = $query->execute();

            if ($result->count() === 0) {
                return $slug . '-1';
            } else {
                $number = intval(substr($result->getFirst()->getSlug(), strlen($slug) + 1)) + 1;
                return $slug . '-' . $number;
            }
        }
    }

    /**
     * Deletes all entries from the eventindex for a given event UID using a native database query.
     *
     * @param int $eventUid
     */
    public function removeAllNative($eventUid)
    {
        // We use a normal database query to improve the performance.
        $builder = $this->getQueryBuilder('tx_czsimplecal_domain_model_eventindex');
        $builder->delete('tx_czsimplecal_domain_model_eventindex')
            ->where(
                $builder->expr()->eq(
                    'event',
                    $builder->createNamedParameter((int)$eventUid, PDO::PARAM_INT)
                )
            )
            ->execute();
    }

    /**
     * do the cleaning of the values so that no wrong variable type or value will be used
     *
     * @param $settings
     * @return array
     */
    protected function cleanSettings($settings)
    {
        // Unset unknown fields
        $settings = array_intersect_key($settings, self::$filterSettings);

        $settings = filter_var_array($settings, self::$filterSettings);

        if (isset($settings['filter'])) {
            $settings['filter'] = $this->setupFilters($settings['filter']);
        }

        return $settings;
    }

    /**
     * find all events matching some settings and count them
     *
     * TODO: (ugly) doing dozens of database requests
     *
     * @param array $settings
     * @return array|int
     */
    protected function doCountAllWithSettings($settings = [])
    {
        if (!isset($settings['groupBy'])) {
            return $this->setupSettings($settings)->count();
        } else {
            $output = [];
            if ($settings['groupBy'] === 'day') {
                $step = '+1 day';
            } elseif ($settings['groupBy'] === 'week') {
                $step = '+1 week';
            } elseif ($settings['groupBy'] === 'month') {
                $step = '+1 month';
            } elseif ($settings['groupBy'] === 'year') {
                $step = '+1 year';
            } else {
                $step = null;
            }

            $startDate = new DateTime('@' . $settings['startDate']);
            $startDate->setTimezone(new DateTimeZone(date_default_timezone_get()));

            $endDate = $settings['endDate'];
            while ($startDate->getTimestamp() < $endDate) {
                if ($step === null) {
                    $tempEndDate = null;
                } else {
                    $tempEndDate = clone $startDate;
                    $tempEndDate->modify($step . ' -1 second');
                }

                $output[] = [
                    'date' => $startDate->format('c'),
                    'count' => $this->doCountAllWithSettings(
                        array_merge(
                            $settings,
                            [
                                'startDate' => $startDate->format('U'),
                                'endDate' => $tempEndDate ? $tempEndDate->format('U') : null,
                                'groupBy' => null,
                            ]
                        )
                    ),
                ];

                if ($step === null) {
                    break;
                } else {
                    $startDate->modify($step);
                }
            }

            return $output;
        }
    }

    protected function getQueryBuilder(string $table): QueryBuilder
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        return $connectionPool->getQueryBuilderForTable($table);
    }

    /**
     * check if a given value is a filter
     *
     * @param mixed $filter
     * @return bool
     */
    protected function isFilter($filter)
    {
        return !is_array($filter) || array_key_exists('negate', $filter) || array_key_exists('value', $filter);
    }

    /**
     * sanitizing the given filters
     *
     * @param $filters
     * @param $prefix
     * @return array
     */
    protected function setupFilters($filters, $prefix = '')
    {
        if (!is_array($filters)) {
            return null;
        }

        $return = [];

        foreach ($filters as $name => $filter) {
            if ($this->isFilter($filter)) {
                $cleanedFilter = self::sanitizeFilter($filter);
                if (!is_null($cleanedFilter)) {
                    $return[$prefix . $name] = $cleanedFilter;
                }
            } else {
                if (is_array($filter)) {
                    $return = array_merge(
                        $return,
                        $this->setupFilters($filter, $prefix . $name . '.')
                    );
                }
            }
        }
        return $return;
    }

    /**
     * setup settings for an query
     *
     * possible restrictions are:
     *
     *  * startDate             integer timestamp of the start
     *  * endDate               integer timestamp of the end
     *  * order                 string  the mode to sort by (could be 'asc' or 'desc')
     *  * orderBy               string  the field to sort by (could be 'start' or 'end')
     *  * maxEvents             integer the maximum of events to return
     *  * includeStartedEvents  boolean if events that were in progress on the startDate should be shown
     *  * excludeOverlongEvents boolean if events that were not yet finished on the endDate should be excluded
     *  * filter                array   key is field name and value the desired value, multiple filters are concated
     * with "AND"
     *
     * all given values must be sanitized
     *
     * TODO: (ugly) doing dozens of database requests extbase query needs a better fluent interface for query creation
     *
     * @param array $settings
     * @param $query
     * @return QueryInterface
     * @throws InvalidArgumentException
     */
    protected function setupSettings($settings = [], $query = null)
    {
        if (is_null($query)) {
            $query = $this->createQuery();
        }

        if (isset($settings['startDate'])) {
            $constraint = $query->greaterThanOrEqual(
                $settings['includeStartedEvents'] ? 'end' : 'start',
                $settings['startDate']
            );
        }

        if (isset($settings['endDate'])) {
            $temp_constraint = $query->lessThanOrEqual(
                $settings['excludeOverlongEvents'] ? 'end' : 'start',
                $settings['endDate']
            );

            if (isset($constraint)) {
                /** @noinspection PhpMethodParametersCountMismatchInspection */
                $constraint = $query->logicalAnd($constraint, $temp_constraint);
            } else {
                $constraint = $temp_constraint;
            }
        }

        // Filter categories
        if (isset($settings['filter'])) {
            foreach ($settings['filter'] as $name => $filter) {
                if (is_array($filter['value'])) {
                    /** @var ConstraintInterface $temp_constraint */
                    $temp_constraint = $query->in('event.' . $name, $filter['value']);

                    if (isset($filter['negate']) && $filter['negate']) {
                        $temp_constraint = $query->logicalNot($temp_constraint);
                    }

                    if (isset($constraint)) {
                        /** @noinspection PhpMethodParametersCountMismatchInspection */
                        $constraint = $query->logicalAnd($constraint, $temp_constraint);
                    } else {
                        $constraint = $temp_constraint;
                    }
                }
                // @todo: support for atomic values
            }
        }

        // All constraints should be gathered here
        // Set the WHERE part
        if (isset($constraint)) {
            $query->matching($constraint);
        }

        if (isset($settings['maxEvents'])) {
            $query->setLimit(intval($settings['maxEvents']));
        }

        // Order and orderBy
        if (isset($settings['order']) || isset($settings['orderBy'])) {
            if (!isset($settings['orderBy'])) {
                $orderBy = 'start';
            } elseif ($settings['orderBy'] === 'start' || $settings['orderBy'] === 'startDate') {
                $orderBy = 'start';
            } elseif ($settings['orderBy'] === 'end' || $settings['orderBy'] === 'endDate') {
                $orderBy = 'end';
            } else {
                throw new InvalidArgumentException('"orderBy" should be one of "start" or "end".');
            }

            if (!isset($settings['order'])) {
                $order = QueryInterface::ORDER_ASCENDING;
            } elseif (strtolower($settings['order']) === 'asc') {
                $order = QueryInterface::ORDER_ASCENDING;
            } elseif (strtolower($settings['order']) === 'desc') {
                $order = QueryInterface::ORDER_DESCENDING;
            } else {
                throw new InvalidArgumentException('"order" should be one of "asc" or "desc".');
            }

            $query->setOrderings([$orderBy => $order]);
        }

        return $query;
    }
}
