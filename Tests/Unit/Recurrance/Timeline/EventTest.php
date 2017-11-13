<?php

namespace Tx\CzSimpleCal\Tests\Unit\Recurrance\Timeline;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Tx\CzSimpleCal\Domain\Model\Event;
use Tx\CzSimpleCal\Recurrance\Timeline\Base;
use Tx\CzSimpleCal\Recurrance\Timeline\Event as TimelineEvent;

/**
 * testing the features of Tx_CzSimpleCal_Recurrance_Timeline_Event
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class EventTest extends UnitTestCase
{
    /**
     * @var TimelineEvent
     */
    protected $timeline = null;

    public function setUp()
    {
        $this->timeline = new TimelineEvent();
    }

    public function testAddingOfEvent()
    {
        $event = new Event();

        $this->timeline->setEvent($event);

        $this->timeline->add(
            [
                'start' => strtotime('2009-02-13 23:31:30GMT'),
                'end' => strtotime('2009-02-13 23:31:31GMT'),
            ],
            $event
        );

        $current = $this->timeline->current();

        self::assertArrayHasKey('event', $current, 'data gets key "event"');
        self::assertArrayHasKey('pid', $current, 'data gets key "pid"');
    }

    public function testInheritance()
    {
        self::assertTrue(
            $this->timeline instanceof Base,
            'inherits from Tx_CzSimpleCal_Recurrance_Timeline_Base'
        );
    }
}
