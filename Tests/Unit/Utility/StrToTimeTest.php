<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Tests\Unit\Utility;

use Tx\CzSimpleCal\Utility\StrToTime;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * testing the features of StrToTime
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 * @see http://www.php.net/manual/en/datetime.formats.relative.php
 */
class StrToTimeTest extends UnitTestCase
{
    protected $dateTime = null;

    protected $defaultTimezone = null;

    public function setUp()
    {
        $this->dateTime = strtotime('2009-02-13 23:31:30GMT'); // A friday

        $this->defaultTimezone = date_default_timezone_get();
        date_default_timezone_set('GMT');
    }

    public function tearDown()
    {
        date_default_timezone_set($this->defaultTimezone);
    }

    public function provider()
    {
        $array = [
            [
                '+1 second',
                1234567890 + 1,
            ],
            [
                '-1 second',
                1234567890 - 1,
            ],
            [
                '+1 minute',
                1234567890 + 60,
            ],
            [
                '-1 minute',
                1234567890 - 60,
            ],
            [
                '+1 hour',
                1234567890 + 3600,
            ],
            [
                '-1 hour',
                1234567890 - 3600,
            ],
            [
                '+1 day',
                1234567890 + 86400,
            ],
            [
                '-1 day',
                1234567890 - 86400,
            ],
            [
                '+1 week',
                1234567890 + 7 * 86400,
            ],
            [
                '-1 week',
                1234567890 - 7 * 86400,
            ],
            [
                '+1 month',
                1234567890 + 28 * 86400,
            ],
            [
                '-1 month',
                1234567890 - 31 * 86400,
            ],
            [
                '+1 year',
                1234567890 + 365 * 86400,
            ],
            [
                '-1 year',
                1234567890 - 366 * 86400,
            ],

            [
                'yesterday',
                strtotime('2009-02-12 00:00:00GMT'),
            ],
            [
                'today',
                strtotime('2009-02-13 00:00:00GMT'),
            ],
            [
                'tomorrow',
                strtotime('2009-02-14 00:00:00GMT'),
            ],

            [
                'midnight',
                strtotime('2009-02-13 00:00:00GMT'),
            ],
            [
                'noon',
                strtotime('2009-02-13 12:00:00GMT'),
            ],

            [
                'last monday',
                strtotime('2009-02-09 00:00:00GMT'),
            ],
            [
                'next monday',
                strtotime('2009-02-16 00:00:00GMT'),
            ],

            [
                'monday this week',
                strtotime('2009-02-09 00:00:00GMT'),
            ],
            [
                'monday last week',
                strtotime('2009-02-02 00:00:00GMT'),
            ],
            [
                'monday next week',
                strtotime('2009-02-16 00:00:00GMT'),
            ],
            [
                'sunday this week',
                strtotime('2009-02-15 00:00:00GMT'),
            ],
            [
                'sunday last week',
                strtotime('2009-02-08 00:00:00GMT'),
            ],
            [
                'sunday next week',
                strtotime('2009-02-22 00:00:00GMT'),
            ],

            [
                'first day of this month',
                strtotime('2009-02-01 23:31:30GMT'),
            ],
            [
                'first day of last month',
                strtotime('2009-01-01 23:31:30GMT'),
            ],
            [
                'first day of next month',
                strtotime('2009-03-01 23:31:30GMT'),
            ],
            [
                'last day of this month',
                strtotime('2009-02-28 23:31:30GMT'),
            ],
            [
                'last day of last month',
                strtotime('2009-01-31 23:31:30GMT'),
            ],
            [
                'last day of next month',
                strtotime('2009-03-31 23:31:30GMT'),
            ],

            [
                '1st January this year',
                strtotime('2009-01-01 00:00:00GMT'),
            ],
            [
                '1st January last year',
                strtotime('2008-01-01 00:00:00GMT'),
            ],
            [
                '1st January next year',
                strtotime('2010-01-01 00:00:00GMT'),
            ],
            [
                '31th December this year',
                strtotime('2009-12-31 00:00:00GMT'),
            ],
            [
                '31th December last year',
                strtotime('2008-12-31 00:00:00GMT'),
            ],
            [
                '31th December next year',
                strtotime('2010-12-31 00:00:00GMT'),
            ],
            // Test for compound dates
            [
                'first day of this month | monday this week',
                strtotime('2009-01-26 00:00:00GMT'),
            ],
        ];

        $labels = [];
        foreach ($array as $value) {
            $labels[] = 'with data ' . $value[0];
        }
        return array_combine($labels, $array);
    }

    /**
     * @test
     */
    public function testBasic()
    {
        self::assertEquals(1234567890, $this->dateTime, 'this test is set up correctly');
    }

    /**
     * @dataProvider provider
     */
    public function testModifications($modification, $assumed)
    {
        self::assertEquals(
            $assumed,
            StrToTime::strtotime($modification, $this->dateTime)
        );
    }

    public function testWeeksStartWithMonday()
    {
        self::assertEquals(
            strtotime('2009-02-02 00:00:00GMT'),
            StrToTime::strtotime('monday this week', strtotime('2009-02-08 00:00:00')),
            '"monday this week" when on a sunday'
        );

        self::assertEquals(
            strtotime('2009-02-02 00:00:00GMT'),
            StrToTime::strtotime('monday this week', strtotime('2009-02-02 00:00:00')),
            '"monday this week" when on a monday'
        );

        self::assertEquals(
            strtotime('2009-02-08 00:00:00GMT'),
            StrToTime::strtotime('sunday this week', strtotime('2009-02-08 00:00:00')),
            '"sunday this week" when on a sunday'
        );

        self::assertEquals(
            strtotime('2009-02-08 00:00:00GMT'),
            StrToTime::strtotime('sunday this week', strtotime('2009-02-02 00:00:00')),
            '"sunday this week" when on a monday'
        );

        self::assertEquals(
            strtotime('2009-12-28 00:00:00GMT'),
            StrToTime::strtotime('monday this week', strtotime('2010-01-01 00:00:00')),
            '"monday this week" on a year switch'
        );
    }
}
