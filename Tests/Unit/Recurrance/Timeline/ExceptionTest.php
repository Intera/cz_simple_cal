<?php

namespace Tx\CzSimpleCal\Tests\Unit\Recurrance\Timeline;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Tx\CzSimpleCal\Domain\Model\Event;
use Tx\CzSimpleCal\Recurrance\Timeline\Base;
use Tx\CzSimpleCal\Recurrance\Timeline\Exception;

/**
 * testing the features of Tx_CzSimpleCal_Recurrance_Timeline_Event
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class ExceptionTest extends UnitTestCase
{
    /**
     * @var Event
     */
    protected $event;

    /**
     * @var Exception
     */
    protected $timeline = null;

    public function setUp()
    {
        $this->timeline = new Exception();
        $this->event = new Event();
    }

    public function testInheritance()
    {
        self::assertTrue(
            $this->timeline instanceof Base,
            'inherits from Tx_CzSimpleCal_Recurrance_Timeline_Base'
        );
    }

    public function testTimespansWithSameStart()
    {
        try {
            $this->timeline->add(
                [
                    'start' => strtotime('2009-02-13 23:31:30GMT'),
                    'end' => strtotime('2009-02-13 23:31:31GMT'),
                ],
                $this->event
            );

            $this->timeline->add(
                [
                    'start' => strtotime('2009-02-13 23:31:30GMT'),
                    'end' => strtotime('2009-02-14 00:00:00GMT'),
                ],
                $this->event
            );

            $this->timeline->add(
                [
                    'start' => strtotime('2009-02-13 23:31:30GMT'),
                    'end' => strtotime('2009-02-13 23:45:00GMT'),
                ],
                $this->event
            );
        } catch (\Exception $e) {
            self::assertTrue(false, 'adding two equal exceptions won\'t throw an error.');
            return;
        }

        self::assertSame(
            1,
            $this->timeline->count(),
            'only one exception stored if there was a different one with same start.'
        );

        $current = $this->timeline->current();
        self::assertSame(strtotime('2009-02-14 00:00:00GMT'), $current['end'], 'end of the longest event was stored');
    }
}
