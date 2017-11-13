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

use Tx\CzSimpleCal\Domain\Model\Event as EventModel;

/**
 * holds multiple EventIndices for the same Event
 *
 * to keep it lightweight, when creating EventIndices from Events
 * they are not instanciated as objects, but are represented by an array.
 *
 * This class manages these arrays and takes care of a valid syntax.
 */
class Event extends Base
{
    /**
     * the id of the Event this collection belongs to
     *
     * @var EventModel
     */
    protected $event = null;

    public function current()
    {
        $this->initOutput();
        return $this->addEvent(current($this->data));
    }

    public function next()
    {
        $this->initOutput();
        if ($this->nextAsCurrent) {
            $this->nextAsCurrent = false;
            return $this->addEvent(current($this->data));
        }
        return $this->addEvent(next($this->data));
    }

    /**
     * set the id of the Event this collection belongs to
     *
     * @param EventModel $event
     * @return Event
     */
    public function setEvent($event)
    {
        $this->event = $event;
        return $this;
    }

    /**
     * add the model id to an EventIndex
     *
     * @param array $array
     * @return array
     */
    protected function addEvent($array)
    {
        $array['event'] = $this->event;
        $array['pid'] = $this->event->getPid();
        return $array;
    }
}
