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

use Tx\CzSimpleCal\Domain\Model\Event;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Repository for Event domain models.
 *
 * @method Event findByUid($uid)
 */
class EventRepository extends Repository
{
    /**
     * find all events by a given user id
     *
     * @param string $userId
     * @return QueryResultInterface
     */
    public function findAllByUserId($userId)
    {
        if (!$userId) {
            return null;
        }

        $query = $this->createQuery();

        $query->matching($query->equals('cruser_fe', $userId));

        $query->setOrderings(
            [
                'start_day' => QueryInterface::ORDER_DESCENDING,
            ]
        );

        return $query->execute();
    }

    /**
     * find all records regardless of their storage page, enable fields or language
     *
     * @return QueryResultInterface
     */
    public function findAllEverywhere()
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->
        setRespectStoragePage(false)->
        setIgnoreEnableFields(true)->
        setRespectSysLanguage(false);

        return $query->execute();
    }

    /**
     * find a record by its uid regardless of its pid
     *
     * @param $uid
     * @return Event
     */
    public function findOneByUidEverywhere($uid)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()
            ->setRespectStoragePage(false)
            ->setIgnoreEnableFields(true)
            ->setRespectSysLanguage(false);

        $query->setLimit(1);
        $query->matching($query->equals('uid', $uid));

        $result = $query->execute();
        if (count($result) < 1) {
            return null;
        }

        $object = $result->getFirst();

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $object;
    }

    /**
     * find records for the indexing task
     * with parameters suitable for the indexer
     *
     * @param integer $limit
     * @param integer $maxAge UNIX timestamp
     * @return QueryResultInterface
     */
    public function findRecordsForReindexing($limit = null, $maxAge = null)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()
            ->setRespectStoragePage(false)
            ->setIgnoreEnableFields(true)
            ->setRespectSysLanguage(true);

        if (!is_null($limit)) {
            $query->setLimit($limit);
        }
        if (is_null($maxAge)) {
            // If: no maxAge is set, fetch the oldest events
            $query->setOrderings(
                [
                    'last_indexed' => QueryInterface::ORDER_ASCENDING,
                ]
            );
        } else {
            $query->matching(
                $query->lessThan('last_indexed', $maxAge)
            );
            /* no sorting here:
             * - sorting would make the query slower
             * - multiple parallel scheduler tasks could do the same work as there is no locking
             *    with this "random" sorting, there is at least a chance this won't happen
             */
        }
        return $query->execute();
    }

    /**
     * get the UNIX timestamp of the indexing of the oldest event that needs indexing
     *
     * @return integer UNIX timestamp
     */
    public function getMaxIndexAge()
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->
        setRespectStoragePage(false)->
        setIgnoreEnableFields(true)->
        setRespectSysLanguage(false);
        $query->setOrderings(
            [
                'last_indexed' => QueryInterface::ORDER_ASCENDING,
            ]
        );

        $query->setLimit(1);

        $result = $query->execute();

        if ($result->count() == 0) {
            return null;
        } else {
            return $result->getFirst()->getLastIndexed()->format('U');
        }
    }
}
