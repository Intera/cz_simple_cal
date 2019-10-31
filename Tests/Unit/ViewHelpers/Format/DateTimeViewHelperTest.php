<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Tests\Unit\ViewHelper\Format;

use DateTime;
use Tx\CzSimpleCal\Tests\Unit\ViewHelpers\IndexedArgumentsTrait;
use Tx\CzSimpleCal\ViewHelpers\Format\DateTimeViewHelper;
use TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase;

/**
 * testing the features of the Format_DateTimeViewHelper
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class DateTimeViewHelperTest extends ViewHelperBaseTestcase
{
    use IndexedArgumentsTrait;

    protected $defaultTimezone = null;

    /**
     * @var DateTimeViewHelper
     */
    protected $viewHelper = null;

    public function setUp()
    {
        parent::setUp();
        $this->viewHelper = new DateTimeViewHelper();

        $this->defaultTimezone = date_default_timezone_get();
        date_default_timezone_set('GMT');
    }

    public function tearDown()
    {
        date_default_timezone_set($this->defaultTimezone);
    }

    public function testFormatParameter()
    {
        $this->initArguments(1234567890, '%Y-%m-%d %H:%M:%S');
        self::assertEquals(
            '2009-02-13 23:31:30',
            $this->viewHelper->render(),
            'format can be set.'
        );
        // @todo: not sure how to test localization of strings
    }

    public function testGetParameter()
    {
        $this->initArguments(
            1234567890,
            '%Y-%m-%d %H:%M:%S',
            '+1 day'
        );
        self::assertEquals(
            '2009-02-14 23:31:30',
            $this->viewHelper->render(),
            'adding one day works'
        );
    }

    public function testTimestampParameter()
    {
        $this->initArguments(null);
        self::assertEquals(
            date('Y-m-d', time()),
            $this->viewHelper->render(),
            'if no timestamp is given the current time is used.'
        );

        $this->initArguments(1234567890);
        self::assertEquals('2009-02-13', $this->viewHelper->render(), 'integer values are excepted.');

        $this->initArguments('1234567890');
        self::assertEquals(
            '2009-02-13',
            $this->viewHelper->render(),
            'if string only contains numbers it is used as timestamp.'
        );

        $this->initArguments('2009-02-13 23:31:30GMT');
        self::assertEquals(
            '2009-02-13',
            $this->viewHelper->render(),
            'if string does not only consist of numbers strtotime is used.'
        );

        $this->initArguments(new DateTime('2009-02-13 23:31:30GMT'));
        self::assertEquals(
            '2009-02-13',
            $this->viewHelper->render(),
            'DateTime objects are accepted.'
        );
    }
}
