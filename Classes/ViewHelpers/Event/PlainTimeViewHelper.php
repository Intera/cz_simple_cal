<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\ViewHelpers\Event;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "cz_simple_cal_mods".       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Tx\CzSimpleCal\Domain\Model\EventIndex;
use Tx\CzSimpleCal\Utility\EventTimeUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Renders the plaing event time without HTML tags.
 */
class PlainTimeViewHelper extends AbstractViewHelper
{
    /**
     * @var EventTimeUtility
     */
    protected $eventTimeTypeUtility;

    public function initializeArguments()
    {
        $this->registerArgument('eventIndex', EventIndex::class, '', true);
    }

    public function injectEventTimeTypeUtility(EventTimeUtility $eventTimeTypeUtility)
    {
        $this->eventTimeTypeUtility = $eventTimeTypeUtility;
    }

    /**
     * Returns the event time type for the given event (can be used in
     * the switch view helper).
     *
     * @return string
     */
    public function render()
    {
        return $this->eventTimeTypeUtility->getPlainEventTimeForEventIndexEntry($this->arguments['eventIndex']);
    }
}
