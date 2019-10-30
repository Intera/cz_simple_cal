<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\ViewHelpers\Event;

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

use InvalidArgumentException;
use RuntimeException;
use Tx\CzSimpleCal\Domain\Model\Event;
use Tx\CzSimpleCal\Domain\Model\EventIndex;
use Tx\CzSimpleCal\Utility\StrToTime;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use UnexpectedValueException;

/**
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class GroupViewHelper extends AbstractViewHelper
{
    protected $orderGetMethodName = null;

    public function initializeArguments()
    {
        $this->registerArgument('events', 'array', 'the events', true);
        $this->registerArgument('as', 'string', 'the variable name the group events should be written to', true);
        $this->registerArgument('by', 'string', 'one of the supported types', false, 'day');
        $this->registerArgument('orderBy', 'string', '', false, '');
    }

    /**
     * @return string
     */
    public function render()
    {
        $events = $this->arguments['events'];
        $as = $this->arguments['as'];
        $by = $this->arguments['by'];

        $by = strtolower($by);
        if ($by === 'day') {
            $events = $this->groupByTime($events, 'midnight');
        } elseif ($by === 'month') {
            $events = $this->groupByTime($events, 'first day of this month midnight');
        } elseif ($by === 'year') {
            $events = $this->groupByTime($events, 'january 1st midnight');
        } elseif ($by === 'location') {
            $events = $this->groupByLocation($events);
        } elseif ($by === 'organizer') {
            $events = $this->groupByOrganizer($events);
        } else {
            throw new InvalidArgumentException(
                sprintf('%s can\'t group by "%s". Maybe a misspelling?', get_class($this), $by)
            );
        }

        $this->templateVariableContainer->add($as, $events);
        $output = $this->renderChildren();
        $this->templateVariableContainer->remove($as);

        return $output;
    }

    /**
     * do grouping by location
     *
     * @param array $events
     * @return array
     */
    protected function groupByLocation(array $events): array
    {
        $result = [];
        /** @var Event $event */
        foreach ($events as $event) {
            $locationKey = $event->getActiveLocation() ? $event->getActiveLocation()->getUid() : 0;
            if (!array_key_exists($locationKey, $result)) {
                $result[$locationKey] = [
                    'info' => $event->getActiveLocation() ? $event->getActiveLocation() : false,
                    'events' => [],
                ];
            }

            $result[$locationKey]['events'][] = $event;
        }

        return $this->order($result);
    }

    /**
     * do grouping by organizer
     *
     * @param array $events
     * @return array
     */
    protected function groupByOrganizer(array $events): array
    {
        $result = [];
        /** @var Event $event */
        foreach ($events as $event) {
            $organizerKey = $event->getActiveOrganizer() ? $event->getActiveOrganizer()->getUid() : 0;
            if (!array_key_exists($organizerKey, $result)) {
                $result[$organizerKey] = [
                    'info' => $event->getActiveOrganizer() ? $event->getActiveOrganizer() : false,
                    'events' => [],
                ];
            }

            $result[$organizerKey]['events'][] = $event;
        }
        return $this->order($result);
    }

    /**
     * do grouping by some time related constraint
     *
     * @param array $events
     * @param string $time
     * @return array
     */
    protected function groupByTime(array $events, string $time): array
    {
        $result = [];
        /** @var EventIndex $event */
        foreach ($events as $event) {
            $key = StrToTime::strtotime($time, $event->getStart());
            if (!array_key_exists($key, $result)) {
                $result[$key] = [
                    'info' => $key,
                    'events' => [],
                ];
            }

            $result[$key]['events'][] = $event;
        }
        return $result;
    }

    protected function order(array $events): array
    {
        if (!$this->arguments['orderBy']) {
            return $events;
        }

        $this->orderGetMethodName = 'get' . GeneralUtility::underscoredToUpperCamelCase($this->arguments['orderBy']);
        if (usort($events, [$this, 'orderByObjectMethod'])) {
            return $events;
        } else {
            throw new RuntimeException(sprintf('%s could not sort the events.', get_class($this)));
        }
    }

    protected function orderByObjectMethod($a, $b)
    {
        if (strlen($this->orderGetMethodName) < 5) {
            throw new UnexpectedValueException(sprintf('%s was called without setting a getMethodName', __FUNCTION__));
        }

        $aValue = call_user_func([$a['info'], $this->orderGetMethodName]);
        $bValue = call_user_func([$b['info'], $this->orderGetMethodName]);

        return $aValue < $bValue ? -1 : ($aValue > $bValue ? 1 : 0);
    }
}
