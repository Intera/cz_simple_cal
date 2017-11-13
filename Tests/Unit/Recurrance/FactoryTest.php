<?php

namespace Tx\CzSimpleCal\Tests\Unit\Recurrance;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Tx\CzSimpleCal\Domain\Model\Event;
use Tx\CzSimpleCal\Recurrance\Timeline\Exception;
use Tx\CzSimpleCal\Tests\Unit\Recurrance\Mocks\RecurranceFactoryMock;
use Tx\CzSimpleCal\Tests\Unit\Recurrance\Mocks\TimelineEventMock;

/**
 * testing the features of Tx_CzSimpleCal_Recurrance_FactoryTest
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class FactoryTest extends UnitTestCase
{
    /**
     * @var TimelineEventMock
     */
    protected $events = null;

    /**
     * @var Exception
     */
    protected $exceptions = null;

    /**
     * @var RecurranceFactoryMock
     */
    protected $factory = null;

    public function setUp()
    {
        $this->factory = new RecurranceFactoryMock();
        $this->events = new TimelineEventMock();
        $this->events->setEvent(new Event());
        $this->exceptions = new Exception();
    }

    public function provideDataForEmptyException()
    {
        return [
            '1 event' => [$this->generateFakeData(1)],
            '3 events' => [$this->generateFakeData(3)],
        ];
    }

    public function provideDataForExceptionBeforeEventEnd()
    {
        return [
            '1 event' => [
                $this->generateFakeData(1, '2009-01-01 00:00:00GMT', '+12 hours'),
                $this->generateFakeData(1, '2009-01-01 06:00:00GMT', '+12 hours'),
            ],
        ];
    }

    public function provideDataForExceptionBeforeEventStart()
    {
        return [
            '1 event' => [
                $this->generateFakeData(1, '2009-01-01 06:00:00GMT', '+12 hours'),
                $this->generateFakeData(1, '2009-01-01 00:00:00GMT', '+12 hours'),
            ],
        ];
    }

    public function provideDataForExceptionEndsWhenEventStarts()
    {
        return [
            '1 event' => [
                $this->generateFakeData(1, '2009-01-01 12:00:00GMT', '+12 hours'),
                $this->generateFakeData(1, '2009-01-01 00:00:00GMT', '+12 hours'),
            ],
            'zero length event' => [
                $this->generateFakeData(1, '2009-01-01 12:00:00GMT', 'now'),
                $this->generateFakeData(1, '2009-01-01 00:00:00GMT', '+12 hours'),
            ],
        ];
    }

    public function provideDataForExceptionOverlapsEventCompletely()
    {
        return [
            '1 event' => [
                $this->generateFakeData(1, '2009-01-01 06:00:00GMT', '+3 hours'),
                $this->generateFakeData(1, '2009-01-01 00:00:00GMT', '+12 hours'),
            ],
            'zero length event' => [
                $this->generateFakeData(1, '2009-01-01 06:00:00GMT', 'now'),
                $this->generateFakeData(1, '2009-01-01 00:00:00GMT', '+12 hours'),
            ],

        ];
    }

    public function provideDataForExceptionStartsWhenEventEnds()
    {
        return [
            '1 event' => [
                $this->generateFakeData(1, '2009-01-01 00:00:00GMT', '+12 hours'),
                $this->generateFakeData(1, '2009-01-01 12:00:00GMT', '+6 hours'),
            ],
        ];
    }

    public function provideDataForExceptionWhileEvent()
    {
        return [
            '1 event' => [
                $this->generateFakeData(1, '2009-01-01 00:00:00GMT', '+12 hours'),
                $this->generateFakeData(1, '2009-01-01 06:00:00GMT', '+3 hours'),
            ],
        ];
    }

    public function provideDataForNonMatchingException()
    {
        return [
            '1 event' => [
                $this->generateFakeData(1, '2009-01-01 00:00:00GMT', '+12 hours'),
                $this->generateFakeData(1, '2009-01-01 14:00:00GMT', '+1 hour'),
            ],
            '3 events' => [
                $this->generateFakeData(3, '2009-01-01 00:00:00GMT', '+12 hours', '+1 day'),
                $this->generateFakeData(3, '2009-01-01 14:00:00GMT', '+1 hour', '+1 day'),
            ],
            'zero length exceptions (3 events)' => [
                $this->generateFakeData(3, '2009-01-01 00:00:00GMT', '+12 hours', '+1 day'),
                $this->generateFakeData(3, '2009-01-01 14:00:00GMT', 'now', '+1 day'),
            ],
            'zero length events (3 events)' => [
                $this->generateFakeData(3, '2009-01-01 00:00:00GMT', 'now', '+1 day'),
                $this->generateFakeData(3, '2009-01-01 14:00:00GMT', '+1 hour', '+1 day'),
            ],

        ];
    }

    /**
     * exceptions : ######
     *                  ######
     * events     :   ##########
     *                      ######
     */
    public function testComplexExamplePattern1()
    {
        $exceptionData = array_merge(
            $this->generateFakeData(1, '2009-01-01 00:00:00GMT', '+3 hours'),
            $this->generateFakeData(1, '2009-01-01 02:00:00GMT', '+3 hours')
        );
        $eventData = array_merge(
            $this->generateFakeData(1, '2009-01-01 01:00:00GMT', '+5 hours'),
            $this->generateFakeData(1, '2009-01-01 03:00:00GMT', '+4 hours')
        );

        $this->setEventData($eventData);
        $this->setExceptionData($exceptionData);

        $events = $this->factory->dropExceptionalEvents($this->events, $this->exceptions);

        self::assertEquals(0, $events->count(), 'all events are unset');
    }

    /**
     *
     * this pattern is repeated 30 times:
     *
     *              |           |           |           |           |
     * exceptions : ############            ############
     * events     : ####                                        ####
     *                            ########
     *                              ####
     *                                ######
     *                                  ########
     */
    public function testComplexExamplePattern2()
    {
        $exceptionData = $this->generateFakeData(60, '2009-01-01 00:00:00GMT', '+6 hours', '+12 hours');
        $eventData = array_merge(
            $this->generateFakeData(30, '2008-12-31 22:00:00GMT', '+4 hours', '+1 day'),
            $this->generateFakeData(30, '2009-01-01 07:00:00GMT', '+4 hours', '+1 day'),
            $this->generateFakeData(30, '2009-01-01 08:00:00GMT', '+2 hours', '+1 day'),
            $this->generateFakeData(30, '2009-01-01 09:00:00GMT', '+3 hours', '+1 day'),
            $this->generateFakeData(30, '2009-01-01 10:00:00GMT', '+4 hours', '+1 day')
        );

        $this->setEventData($eventData);
        $this->setExceptionData($exceptionData);

        $events = $this->factory->dropExceptionalEvents($this->events, $this->exceptions);

        self::assertEquals(90, $events->count(), 'correct count of events');
    }

    /**
     * what if no exceptions are given at all
     *
     * @dataProvider provideDataForEmptyException
     */
    public function testEmptyException($eventData)
    {
        $this->setEventData($eventData);

        $events = $this->factory->dropExceptionalEvents($this->events, $this->exceptions);

        self::assertEquals($eventData, $events->getData(), 'data remains unchanged');

        $returnTimespan = current($events);
    }

    /**
     * multiple events are affected by one exception
     *
     * exception :   ######
     *
     * events    : ####
     *               ####
     *                 ####
     *                   ####
     */
    public function testExceptionAffectsMultipleEvents()
    {
        $eventData = $this->generateFakeData(24, '2009-01-01 23:00:00GMT', '+2 hours', '+1 hour');
        $exceptionData = $this->generateFakeData(1, '2009-01-02 00:00:00GMT', '+1 day');

        $this->setEventData($eventData);
        $this->setExceptionData($exceptionData);

        $events = $this->factory->dropExceptionalEvents($this->events, $this->exceptions);

        self::assertEquals(0, $events->count(), 'all events are unset');
    }

    /**
     * what if an exception starts before an event ends but exception finishes afterwards
     *
     * event     : ########
     * exception :     ########
     *
     * @dataProvider provideDataForExceptionBeforeEventEnd
     */
    public function testExceptionBeforeEventEnd($eventData, $exceptionData)
    {
        $this->setEventData($eventData);
        $this->setExceptionData($exceptionData);

        $events = $this->factory->dropExceptionalEvents($this->events, $this->exceptions);

        self::assertEquals(0, $events->count(), 'all events are unset');

        $returnTimespan = current($events);
    }

    /**
     * what if an exceptions has started when the event starts, but stops earlier than the event
     *
     * event     :     ########
     * exception : ########
     *
     * @dataProvider provideDataForExceptionBeforeEventStart
     */
    public function testExceptionBeforeEventStart($eventData, $exceptionData)
    {
        $this->setEventData($eventData);
        $this->setExceptionData($exceptionData);

        $events = $this->factory->dropExceptionalEvents($this->events, $this->exceptions);

        self::assertEquals(0, $events->count(), 'all events are unset');

        $returnTimespan = current($events);
    }

    /**
     * what if an exception ends excactly when an event starts
     *
     * event     :     ########
     * exception : ####
     *
     * @dataProvider provideDataForExceptionEndsWhenEventStarts
     */
    public function testExceptionEndsWhenEventStarts($eventData, $exceptionData)
    {
        $this->setEventData($eventData);
        $this->setExceptionData($exceptionData);

        $events = $this->factory->dropExceptionalEvents($this->events, $this->exceptions);

        self::assertEquals($eventData, $events->getData(), 'data remains unchanged');

        $returnTimespan = current($events);
    }

    /**
     * what if an exception covers a whole event
     *
     * event     :     ########
     * exception : ################
     *
     * @dataProvider provideDataForExceptionOverlapsEventCompletely
     */
    public function testExceptionOverlapsEventCompletely($eventData, $exceptionData)
    {
        $this->setEventData($eventData);
        $this->setExceptionData($exceptionData);

        $events = $this->factory->dropExceptionalEvents($this->events, $this->exceptions);

        self::assertEquals(0, $events->count(), 'all events are unset');

        $returnTimespan = current($events);
    }

    /**
     * what if an exception starts excactly when an atomic event starts
     *
     * event     : |
     * exception : ########
     *
     */
    public function testExceptionStartsWhenAtomicEventStarts()
    {
        $eventData = $this->generateFakeData(1, '2009-01-01 00:00:00GMT', 'now');
        $exceptionData = $this->generateFakeData(1, '2009-01-01 00:00:00GMT', '+6 hours');

        $this->setEventData($eventData);
        $this->setExceptionData($exceptionData);

        $events = $this->factory->dropExceptionalEvents($this->events, $this->exceptions);

        self::assertEquals(0, $events->count(), 'all events are unset');
    }




    /*
     * tests for multiple events
     */

    /**
     * what if an exception starts excactly when an event ends
     *
     * event     : ####
     * exception :     ########
     *
     * @dataProvider provideDataForExceptionStartsWhenEventEnds
     */
    public function testExceptionStartsWhenEventEnds($eventData, $exceptionData)
    {
        $this->setEventData($eventData);
        $this->setExceptionData($exceptionData);

        $events = $this->factory->dropExceptionalEvents($this->events, $this->exceptions);

        self::assertEquals($eventData, $events->getData(), 'data remains unchanged');

        $returnTimespan = current($events);
    }

    /**
     * what if an exception applies just when the event runs
     *
     * event     : ################
     * exception :     ########
     *
     * @dataProvider provideDataForExceptionWhileEvent
     */
    public function testExceptionWhileEvent($eventData, $exceptionData)
    {
        $this->setEventData($eventData);
        $this->setExceptionData($exceptionData);

        $events = $this->factory->dropExceptionalEvents($this->events, $this->exceptions);

        self::assertEquals(0, $events->count(), 'all events are unset');

        $returnTimespan = current($events);
    }

    /**
     * multiple events are affected by one exception
     *
     * event      :   ######
     *
     * exceptions : ####
     *                ####
     *                  ####
     *                    ####
     */
    public function testMultipleExceptionsAffectEvent()
    {
        $exceptionData = $this->generateFakeData(24, '2009-01-01 23:00:00GMT', '+2 hours', '+1 hour');
        $eventData = $this->generateFakeData(1, '2009-01-02 00:00:00GMT', '+1 day');

        $this->setEventData($eventData);
        $this->setExceptionData($exceptionData);

        $events = $this->factory->dropExceptionalEvents($this->events, $this->exceptions);

        self::assertEquals(0, $events->count(), 'all events are unset');
    }

    /**
     * what if exceptions are given, but they don't match any event
     *
     * event     : ########
     * exception :            ########
     *
     * @dataProvider provideDataForNonMatchingException
     */
    public function testNonMatchingException($eventData, $exceptionData)
    {
        $this->setEventData($eventData);
        $this->setExceptionData($exceptionData);

        $events = $this->factory->dropExceptionalEvents($this->events, $this->exceptions);

        self::assertEquals($eventData, $events->getData(), 'data remains unchanged');

        $returnTimespan = current($events);
    }

    protected function generateFakeData(
        $number = 1,
        $startAt = '2009-01-01 00:00:00GMT',
        $length = '+12 hours',
        $gap = '+1 day'
    ) {
        $ret = [];

        $now = strtotime($startAt);

        while ($number-- > 0) {
            $ret[] = [
                'start' => $now,
                'end' => strtotime($length, $now),
            ];
            $now = strtotime($gap, $now);
        }

        return $ret;
    }

    protected function setEventData($data)
    {
        foreach ($data as $timespan) {
            $this->events->add($timespan, new Event());
        }
    }

    protected function setExceptionData($data)
    {
        foreach ($data as $timespan) {
            $this->exceptions->add($timespan, new Event());
        }
    }
}
