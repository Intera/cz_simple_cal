<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Tests\Unit\Utility;

use DateTimeZone;
use Tx\CzSimpleCal\Utility\DateTime;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * testing the DateTime class
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class DateTimeTest extends UnitTestCase
{
    public function provideDataForConstructor()
    {
        $array = [
            ['2009-02-13 23:31:30'],
            [
                '2009-02-13 23:31:30',
                'UTC',
            ],
            [
                '2009-02-13 23:31:30',
                'Europe/Berlin',
            ],
            ['2009-02-13 23:31:30GMT'],
            [
                '2009-02-13 23:31:30GMT',
                'Europe/Berlin',
            ],
            ['@1234567890'],
            [
                '@1234567890',
                'Europe/Berlin',
            ],
        ];
        $labels = [];
        foreach ($array as $value) {
            $labels[] = isset($value[1]) ?
                sprintf('%s and timezone %s', $value[0], $value[1]) :
                sprintf('%s', $value[0]);
        }
        return array_combine($labels, $array);
    }

    public function provideDataForEnhancement()
    {
        $array = [
            [
                '@1234567890|first day of this month',
                strtotime('2009-02-01 23:31:30GMT'),
            ],
            [
                '@1234567890|first day of this month|monday this week',
                strtotime('2009-01-26 00:00:00GMT'),
            ],
        ];
        $labels = [];
        foreach ($array as $value) {
            $labels[] = sprintf('%s is %s', $value[0], gmdate('c', $value[1]));
        }
        return array_combine($labels, $array);
    }

    public function provideDataForFormats()
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

            [
                '2008/6/30',
                strtotime('2008-06-30 00:00:00GMT'),
            ],
            [
                '2008/06/30',
                strtotime('2008-06-30 00:00:00GMT'),
            ],
            [
                '2008-06-30',
                strtotime('2008-06-30 00:00:00GMT'),
            ],
            [
                '30-06-2008',
                strtotime('2008-06-30 00:00:00GMT'),
            ],
            [
                '30.06.2008',
                strtotime('2008-06-30 00:00:00GMT'),
            ],
            [
                '30-June 2008',
                strtotime('2008-06-30 00:00:00GMT'),
            ],
            [
                '30-06-2008',
                strtotime('2008-06-30 00:00:00GMT'),
            ],
            [
                '30JUN08',
                strtotime('2008-06-30 00:00:00GMT'),
            ],
            [
                '30 VI 2008',
                strtotime('2008-06-30 00:00:00GMT'),
            ],
            [
                'June 30th, 2008',
                strtotime('2008-06-30 00:00:00GMT'),
            ],
            [
                'June 30, 2008',
                strtotime('2008-06-30 00:00:00GMT'),
            ],
            [
                'June.30,08',
                strtotime('2008-06-30 00:00:00GMT'),
            ],
            [
                'June.30,2008',
                strtotime('2008-06-30 00:00:00GMT'),
            ],
            [
                'Jun-30-08',
                strtotime('2008-06-30 00:00:00GMT'),
            ],
            [
                'Jun-30-2008',
                strtotime('2008-06-30 00:00:00GMT'),
            ],
            [
                '2008-Jun-30',
                strtotime('2008-06-30 00:00:00GMT'),
            ],
            [
                '20080630',
                strtotime('2008-06-30 00:00:00GMT'),
            ],
            [
                '08-06-30',
                strtotime('2008-06-30 00:00:00GMT'),
            ],
            [
                '2008W273',
                strtotime('2008-07-02 00:00:00GMT'),
            ],
            [
                '2008W27-3',
                strtotime('2008-07-02 00:00:00GMT'),
            ],

            [
                '4 am',
                strtotime('2009-02-13 04:00:00GMT'),
            ],
            [
                '4:08 am',
                strtotime('2009-02-13 04:08:00GMT'),
            ],
            [
                '4:08:37 am',
                strtotime('2009-02-13 04:08:37GMT'),
            ],
            [
                '04:08',
                strtotime('2009-02-13 04:08:00GMT'),
            ],
            [
                '04.08',
                strtotime('2009-02-13 04:08:00GMT'),
            ],
            [
                't04:08',
                strtotime('2009-02-13 04:08:00GMT'),
            ],
            [
                '0408',
                strtotime('2009-02-13 04:08:00GMT'),
            ],
            [
                't0408',
                strtotime('2009-02-13 04:08:00GMT'),
            ],
            [
                '04:08:37',
                strtotime('2009-02-13 04:08:37GMT'),
            ],
            [
                '04.08.37',
                strtotime('2009-02-13 04:08:37GMT'),
            ],
            [
                '040837',
                strtotime('2009-02-13 04:08:37GMT'),
            ],
            [
                '040837CEST',
                strtotime('2009-02-13 02:08:37GMT'),
            ],

            [
                '2008-07-01T22:35:17',
                strtotime('2008-07-01 22:35:17GMT'),
            ],
            [
                '@1215282385',
                strtotime('2008-07-05 18:26:25GMT'),
            ],
        ];

        $labels = [];
        foreach ($array as $value) {
            $labels[] = $value[0];
        }
        return array_combine($labels, $array);
    }

    /**
     * @dataProvider provideDataForConstructor
     */
    public function testConstructorCompliance($format, $timezone = null)
    {
        if (is_null($timezone)) {
            $dateTime = new DateTime($format);
            $dateTimeExt = new DateTime($format);
        } else {
            $dateTime = new DateTime($format, new DateTimeZone($timezone));
            $dateTimeExt = new DateTime($format, new DateTimeZone($timezone));
        }
        self::assertEquals(
            $dateTime->format('c'),
            $dateTimeExt->format('c'),
            'constructors of DateTime and Utility_DateTime behave the same.'
        );
    }

    /**
     * @dataProvider provideDataForEnhancement
     */
    public function testConstructorEnhancement($format, $expected)
    {
        $dateTime = new DateTime($format);
        $this->assertEquals($expected, $dateTime->format('U'));
    }

    /**
     * @dataProvider provideDataForEnhancement
     */
    public function testModifyEnhancement($format, $expected)
    {
        $format = explode('|', $format);
        $init = array_shift($format);
        $format = implode($format, '|');

        $dateTime = new DateTime($init);
        $dateTime->modify($format);
        $this->assertEquals($expected, $dateTime->format('U'));
    }

    /**
     * @dataProvider provideDataForFormats
     */
    public function testRecognizedFormats($format, $expected)
    {
        $dateTime = new DateTime('@1234567890');
        $dateTime->modify($format);

        $this->assertEquals($expected, $dateTime->format('U'));
    }
}
