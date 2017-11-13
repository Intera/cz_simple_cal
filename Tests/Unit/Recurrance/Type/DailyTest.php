<?php

namespace Tx\CzSimpleCal\Tests\Unit\Recurrance\Type;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Tx\CzSimpleCal\Recurrance\Timeline\Base;
use Tx\CzSimpleCal\Recurrance\Type\Daily;
use Tx\CzSimpleCal\Tests\Unit\Recurrance\Type\Mocks\IsRecurringMock;

/**
 * testing the features of Tx_CzSimpleCal_Recurrance_Type_Daily
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class DailyTest extends UnitTestCase
{
    public function testIfOnlyStartIsSignificant()
    {
        $event = IsRecurringMock::fromArray(
            [
                'start' => '2009-02-13 23:31:30GMT',
                'end' => '2009-02-15 16:00:00GMT',
                'recurrance_until' => '2009-02-14 23:59:59GMT',
            ]
        );

        $return = $this->buildRecurrance($event);

        self::assertEquals(2, count($return->toArray()), 'exactly two event returned.');
    }

    public function testRecurranceUntil()
    {
        $event = IsRecurringMock::fromArray(
            [
                'start' => '2009-02-13 23:31:30GMT',
                'end' => '2009-02-13 23:31:30GMT',
                'recurrance_until' => '2009-02-14 23:59:59GMT',
            ]
        );

        $return = $this->buildRecurrance($event);

        self::assertEquals(2, count($return->toArray()), 'exactly two events returned');
        self::assertEquals(
            [
                'start' => strtotime('2009-02-13 23:31:30GMT'),
                'end' => strtotime('2009-02-13 23:31:30GMT'),
            ],
            $return->current(),
            'this event equals the input settings'
        );
        self::assertEquals(
            [
                'start' => strtotime('2009-02-14 23:31:30GMT'),
                'end' => strtotime('2009-02-14 23:31:30GMT'),
            ],
            $return->next(),
            'times are preserved'
        );
    }

    protected function buildRecurrance($event, $timeline = null)
    {
        if (is_null($timeline)) {
            $timeline = new Base();
        }
        $typeDaily = new Daily();

        return $typeDaily->build($event, $timeline);
    }
}
