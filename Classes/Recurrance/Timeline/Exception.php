<?php

namespace Tx\CzSimpleCal\Recurrance\Timeline;

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

use Tx\CzSimpleCal\Domain\Model\Enumeration\EventStatus;
use Tx\CzSimpleCal\Domain\Model\Enumeration\ExceptionType;
use Tx\CzSimpleCal\Domain\Model\Exception as ExceptionModel;

/**
 * Exception in the timeline.
 */
class Exception extends Base
{
    /**
     * @param array $data
     * @param \Tx\CzSimpleCal\Domain\Interfaces\IsRecurring $event
     * @return Exception
     */
    public function add($data, $event)
    {
        try {
            parent::add($data, $event);
        } catch (\UnexpectedValueException $e) {
            // Catched if an exception with this start-date already exists
            $key = $data['start'];
            if ($this->data[$key]['end'] < $data['end']) {
                // If the set exception is shorter -> override it
                $this->data[$key] = $data;
            }
        }
        return $this;
    }

    /**
     * Loads some additional data that should be overwritten in the original event.
     *
     * @param \Tx\CzSimpleCal\Domain\Model\BaseEvent $exception
     */
    protected function initAdditionalEventData($exception)
    {
        if (!$exception instanceof ExceptionModel) {
            return;
        }
        if (!$exception->getType()->equals(ExceptionType::UPDATE_EVENT)) {
            return;
        }

        $additionalData = [];

        if ($exception->getStatus() !== EventStatus::UNDEFINED) {
            $additionalData['status'] = $exception->getStatus();
        }

        if ($exception->getTeaser() !== null) {
            $additionalData['teaser'] = $exception->getTeaser();
        }

        $this->data[$this->lastValue]['additionalEventData'] = $additionalData;
    }
}
