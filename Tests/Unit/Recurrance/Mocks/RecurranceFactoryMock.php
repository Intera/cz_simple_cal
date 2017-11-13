<?php

namespace Tx\CzSimpleCal\Tests\Unit\Recurrance\Mocks;

use Tx\CzSimpleCal\Recurrance\RecurranceFactory;
use Tx\CzSimpleCal\Recurrance\Timeline\Event as TimelineEvent;
use Tx\CzSimpleCal\Recurrance\Timeline\Exception as TimelineException;

class RecurranceFactoryMock extends RecurranceFactory
{
    /**
     * @param TimelineEvent $events
     * @param TimelineException $exceptions
     * @return TimelineEventMock
     */
    public function dropExceptionalEvents($events, $exceptions)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::dropExceptionalEvents($events, $exceptions);
    }
}
