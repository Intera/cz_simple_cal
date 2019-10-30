<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\ViewHelpers;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "cz_simple_cal".            *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Int\CzSimpleCal\Utility\EventTimeUtility;
use Tx\CzSimpleCal\Domain\Model\Event;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Retrieves the event time type of a given event.
 */
class EventTimeTypeViewHelper extends AbstractViewHelper
{
    /**
     * @var EventTimeUtility
     */
    protected $eventTimeTypeUtility;

    public function initializeArguments()
    {
        $this->registerArgument('event', Event::class, '', true);
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
    public function render(): string
    {
        $event = $this->arguments['event'];
        return $this->eventTimeTypeUtility->getEventTimeType($event);
    }
}
