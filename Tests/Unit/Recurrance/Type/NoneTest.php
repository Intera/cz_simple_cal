<?php

namespace Tx\CzSimpleCal\Tests\Unit\Recurrance\Type;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Tx\CzSimpleCal\Recurrance\Timeline\Base;
use Tx\CzSimpleCal\Recurrance\Type\None;
use Tx\CzSimpleCal\Tests\Unit\Recurrance\Type\Mocks\IsRecurringMock;

/**
 * testing the features of Tx_CzSimpleCal_Recurrance_Type_Weekly
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class NoneTest extends UnitTestCase
{
    public function testBasic()
    {
        $event = IsRecurringMock::fromArray(
            [
                'start' => '2009-02-13 23:31:30GMT',
                'end' => '2009-02-13 23:31:30GMT',
            ]
        );

        $return = $this->buildRecurrance($event);

        self::assertEquals(1, count($return->toArray()), 'exactly one event returned');
        self::assertEquals(
            [
                'start' => strtotime('2009-02-13 23:31:30GMT'),
                'end' => strtotime('2009-02-13 23:31:30GMT'),
            ],
            $return->current(),
            'the event equals the input settings'
        );
    }

    protected function buildRecurrance($event, $timeline = null)
    {
        if (is_null($timeline)) {
            $timeline = new Base();
        }
        $typeNone = new None();

        return $typeNone->build($event, $timeline);
    }
}
