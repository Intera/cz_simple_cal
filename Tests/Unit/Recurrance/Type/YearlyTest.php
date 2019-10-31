<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Tests\Unit\Recurrance\Type;

use Tx\CzSimpleCal\Recurrance\Timeline\Base;
use Tx\CzSimpleCal\Recurrance\Type\Yearly;
use Tx\CzSimpleCal\Tests\Unit\Recurrance\Type\Mocks\IsRecurringMock;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * testing the features of Tx_CzSimpleCal_Recurrance_Type_Yearly
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class YearlyTest extends UnitTestCase
{
    /**
     * we test for Good Friday
     */
    public function testRecurranceSubtypeEasterForEarlierDates()
    {
        /* easter dates (sunday)
         *
         * 2009: 2010-04-12
         * 2010: 2010-04-04
         */
        $event = IsRecurringMock::fromArray(
            [
                'start' => '2009-04-10 12:00:00GMT',
                'end' => '2009-04-10 12:00:00GMT',
                'recurrance_until' => '2010-06-01 00:00:00GMT',
                'recurrance_subtype' => 'relativetoeaster',
            ]
        );

        $return = $this->buildRecurrance($event);

        self::assertEquals(2, count($return->toArray()), 'exactly two events returned');
        self::assertEquals(
            [
                'start' => strtotime('2009-04-10 12:00:00GMT'),
                'end' => strtotime('2009-04-10 12:00:00GMT'),
            ],
            $return->current(),
            'first event equals the input settings'
        );
        self::assertEquals(
            [
                'start' => strtotime('2010-04-02 12:00:00GMT'),
                'end' => strtotime('2010-04-02 12:00:00GMT'),
            ],
            $return->next(),
            'times are preserved'
        );
    }

    public function testRecurranceSubtypeEasterForEasterSunday()
    {
        /* easter dates (sunday)
         *
         * 2009: 2009-04-12
         * 2010: 2010-04-04
         */
        $event = IsRecurringMock::fromArray(
            [
                'start' => '2009-04-12 12:00:00GMT',
                'end' => '2009-04-12 12:00:00GMT',
                'recurrance_until' => '2010-06-01 00:00:00GMT',
                'recurrance_subtype' => 'relativetoeaster',
            ]
        );

        $return = $this->buildRecurrance($event);

        self::assertEquals(2, count($return->toArray()), 'exactly two events returned');
        self::assertEquals(
            [
                'start' => strtotime('2009-04-12 12:00:00GMT'),
                'end' => strtotime('2009-04-12 12:00:00GMT'),
            ],
            $return->current(),
            'first event equals the input settings'
        );
        self::assertEquals(
            [
                'start' => strtotime('2010-04-04 12:00:00GMT'),
                'end' => strtotime('2010-04-04 12:00:00GMT'),
            ],
            $return->next(),
            'times are preserved'
        );
    }

    /**
     * we test for Whit Sunday
     */
    public function testRecurranceSubtypeEasterForLaterDates()
    {
        /* pentacoast dates (sunday)
         *
         * 2009: 2010-04-12
         * 2010: 2010-04-04
         */
        $event = IsRecurringMock::fromArray(
            [
                'start' => '2009-05-31 12:00:00GMT',
                'end' => '2009-05-31 12:00:00GMT',
                'recurrance_until' => '2010-06-01 00:00:00GMT',
                'recurrance_subtype' => 'relativetoeaster',
            ]
        );

        $return = $this->buildRecurrance($event);

        self::assertEquals(2, count($return->toArray()), 'exactly two events returned');
        self::assertEquals(
            [
                'start' => strtotime('2009-05-31 12:00:00GMT'),
                'end' => strtotime('2009-05-31 12:00:00GMT'),
            ],
            $return->current(),
            'first event equals the input settings'
        );
        self::assertEquals(
            [
                'start' => strtotime('2010-05-23 12:00:00GMT'),
                'end' => strtotime('2010-05-23 12:00:00GMT'),
            ],
            $return->next(),
            'times are preserved'
        );
    }

    public function testRecurranceUntil()
    {
        $event = IsRecurringMock::fromArray(
            [
                'start' => '2009-02-13 23:31:30GMT',
                'end' => '2009-02-13 23:31:30GMT',
                'recurrance_until' => '2011-02-10 23:59:59GMT',
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
            'first event equals the input settings'
        );
        self::assertEquals(
            [
                'start' => strtotime('2010-02-13 23:31:30GMT'),
                'end' => strtotime('2010-02-13 23:31:30GMT'),
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
        $typeYearly = new Yearly();

        return $typeYearly->build($event, $timeline);
    }
}
