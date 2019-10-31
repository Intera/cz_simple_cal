<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Tests\Unit\Recurrance\Timeline;

use Iterator;
use Tx\CzSimpleCal\Domain\Model\Event;
use Tx\CzSimpleCal\Recurrance\Timeline\Base;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use UnexpectedValueException;

/**
 * testing the features of Tx_CzSimpleCal_Recurrance_Timeline_Base
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class BaseTest extends UnitTestCase
{
    /**
     * @var Event
     */
    protected $event;

    /**
     * @var Base
     */
    protected $timeline = null;

    public function setUp()
    {
        $this->timeline = new Base();
        $this->event = new Event();
    }

    public function testAddingTimespansWithSameStartThrowsError()
    {
        $this->timeline->add(
            [
                'start' => strtotime('2009-02-13 23:31:30GMT'),
                'end' => strtotime('2009-02-13 23:31:31GMT'),
            ],
            $this->event
        );
        try {
            $this->timeline->add(
                [
                    'start' => strtotime('2009-02-13 23:31:30GMT'),
                    'end' => strtotime('2009-02-14 23:31:31GMT'),
                ],
                $this->event
            );

            self::assertTrue(false, 'adding two equal events throws an error.');
        } catch (UnexpectedValueException $e) {
            self::assertTrue(true, 'adding two equal events throws an error.');
        }
    }

    public function testBasic()
    {
        $this->timeline->add(
            [
                'start' => strtotime('2009-02-13 23:31:30GMT'),
                'end' => strtotime('2009-02-13 23:31:31GMT'),
            ],
            $this->event
        );

        self::assertGreaterThanOrEqual(1, count($this->timeline->toArray()), 'timespans can be added');
    }

    /**
     * test if the class implements the iterator interface
     *
     * @depends testBasic
     */
    public function testIterator()
    {
        $first = [
            'start' => strtotime('2009-02-13 23:31:30GMT'),
            'end' => strtotime('2009-02-13 23:31:31GMT'),
        ];

        $second = [
            'start' => strtotime('2009-02-13 23:31:32GMT'),
            'end' => strtotime('2009-02-13 23:31:33GMT'),
        ];

        $this->timeline->add($first, $this->event)->add($second, $this->event);

        self::assertTrue($this->timeline instanceof Iterator, 'the class implements the Iterator-Interface');

        self::assertEquals(
            $first,
            $this->timeline->current(),
            'calling current() for the first time returns the first element.'
        );
        self::assertEquals(
            $first,
            $this->timeline->current(),
            'calling current() for the second time returns the first element again.'
        );
        self::assertEquals(
            $first['start'],
            $this->timeline->key(),
            'calling key() returns the key of the first element.'
        );
        self::assertEquals(
            $second,
            $this->timeline->next(),
            'calling next() for the first time returns the second element again.'
        );
        self::assertEquals(
            $second,
            $this->timeline->current(),
            'calling current() after next() returns the second element again.'
        );
        self::assertSame(false, $this->timeline->next(), 'calling next() after the end of the array returns false.');

        $this->timeline->rewind();
        self::assertEquals(
            $first,
            $this->timeline->current(),
            'calling reset() resets the pointer to the first element.'
        );
    }

    /**
     * test that all added elements are always ordered by their start date
     *
     * @depends testIterator
     */
    public function testOrder()
    {
        $early = [
            'start' => strtotime('2009-02-13 23:31:30GMT'),
            'end' => strtotime('2009-02-13 23:31:31GMT'),
        ];

        $late = [
            'start' => strtotime('2009-02-13 23:31:32GMT'),
            'end' => strtotime('2009-02-13 23:31:33GMT'),
        ];

        $this->timeline->add($early, $this->event)->add($late, $this->event);
        self::assertEquals(
            $early,
            $this->timeline->current(),
            'when adding events in the correct order they are ordered ascending.'
        );

        $this->timeline = new Base();
        $this->timeline->add($late, $this->event)->add($early, $this->event);
        self::assertEquals(
            $early,
            $this->timeline->current(),
            'when adding events in the wrong order they are ordered ascending anyways.'
        );
    }
}
