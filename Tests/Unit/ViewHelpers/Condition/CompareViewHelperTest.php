<?php
/** @noinspection PhpDocSignatureInspection */

declare(strict_types=1);

namespace Tx\CzSimpleCal\Tests\Unit\ViewHelper\Condition;

use InvalidArgumentException;
use Tx\CzSimpleCal\Tests\Unit\ViewHelpers\IndexedArgumentsTrait;
use Tx\CzSimpleCal\ViewHelpers\Condition\CompareViewHelper;
use TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase;

/**
 * testing the features of the Condition_CompareViewHelper
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class CompareViewHelperTest extends ViewHelperBaseTestcase
{
    use IndexedArgumentsTrait;

    /**
     * @var CompareViewHelper
     */
    protected $viewHelper = null;

    protected $viewHelperNode = null;

    protected $viewHelperVariableContainer = null;

    public function setUp()
    {
        parent::setUp();

        $this->initViewHelper();
    }

    public function provideDataForEquals()
    {
        $array = [
            [
                true,
                true,
                true,
            ],
            [
                true,
                42,
                true,
            ],
            [
                true,
                '42',
                true,
            ],
            [
                true,
                42.00,
                true,
            ],
            [
                false,
                0,
                true,
            ],
            [
                false,
                '',
                true,
            ],
            [
                42,
                42,
                true,
            ],
            [
                42,
                '42',
                true,
            ],
            [
                42,
                42.00,
                true,
            ],
            [
                '42',
                42.00,
                true,
            ],

            [
                false,
                true,
                false,
            ],
            [
                false,
                42,
                false,
            ],
            [
                false,
                '42',
                false,
            ],
            [
                false,
                42.00,
                false,
            ],
            [
                4,
                2,
                false,
            ],
            [
                42,
                'foobar',
                false,
            ],
            [
                42,
                42.0001,
                false,
            ],
            [
                'foobar',
                42.00,
                false,
            ],
        ];

        $labels = [];
        foreach ($array as $value) {
            $labels[] = sprintf(
                '%s == %s evaluates %s',
                $this->renderValue($value[0]),
                $this->renderValue($value[1]),
                $value[2] ? 'true' : 'false'
            );
        }
        return array_combine($labels, $array);
    }

    public function provideDataForGreaterThan()
    {
        $array = [
            [
                true,
                true,
                false,
            ],
            [
                false,
                false,
                false,
            ],
            [
                true,
                42,
                false,
            ],
            [
                true,
                '42',
                false,
            ],
            [
                true,
                42.00,
                false,
            ],
            [
                false,
                0,
                false,
            ],
            [
                false,
                '',
                false,
            ],
            [
                42,
                42,
                false,
            ],
            [
                42,
                '42',
                false,
            ],
            [
                42,
                42.00,
                false,
            ],
            [
                '42',
                42.00,
                false,
            ],

            [
                true,
                false,
                true,
            ],
            [
                4,
                2,
                true,
            ],
            [
                '4',
                2,
                true,
            ],
            [
                4.00,
                2,
                true,
            ],
            [
                pi(),
                2,
                true,
            ],

            [
                false,
                true,
                false,
            ],
            [
                2,
                4,
                false,
            ],
            [
                2,
                '4',
                false,
            ],
            [
                2,
                4.00,
                false,
            ],
            [
                2,
                pi(),
                false,
            ],
        ];

        $labels = [];
        foreach ($array as $value) {
            $labels[] = sprintf(
                '%s &gt; %s evaluates %s',
                $this->renderValue($value[0]),
                $this->renderValue($value[1]),
                $value[2] ? 'true' : 'false'
            );
        }
        return array_combine($labels, $array);
    }

    public function provideDataForGreaterThanEquals()
    {
        $array = [
            [
                true,
                true,
                true,
            ],
            [
                false,
                false,
                true,
            ],
            [
                true,
                42,
                true,
            ],
            [
                true,
                '42',
                true,
            ],
            [
                true,
                42.00,
                true,
            ],
            [
                false,
                0,
                true,
            ],
            [
                false,
                '',
                true,
            ],
            [
                42,
                42,
                true,
            ],
            [
                42,
                '42',
                true,
            ],
            [
                42,
                42.00,
                true,
            ],
            [
                '42',
                42.00,
                true,
            ],

            [
                true,
                false,
                true,
            ],
            [
                4,
                2,
                true,
            ],
            [
                '4',
                2,
                true,
            ],
            [
                4.00,
                2,
                true,
            ],
            [
                pi(),
                2,
                true,
            ],

            [
                false,
                true,
                false,
            ],
            [
                2,
                4,
                false,
            ],
            [
                2,
                '4',
                false,
            ],
            [
                2,
                4.00,
                false,
            ],
            [
                2,
                pi(),
                false,
            ],
        ];

        $labels = [];
        foreach ($array as $value) {
            $labels[] = sprintf(
                '%s &gt;= %s evaluates %s',
                $this->renderValue($value[0]),
                $this->renderValue($value[1]),
                $value[2] ? 'true' : 'false'
            );
        }
        return array_combine($labels, $array);
    }

    public function provideDataForLessThan()
    {
        $array = $this->provideDataForGreaterThanEquals();
        $outArray = [];

        foreach ($array as $label => $config) {
            $label = strtr(
                $label,
                [
                    '&gt;=' => '&lt;',
                    'evaluates true' => 'evaluates false',
                    'evaluates false' => 'evaluates true',
                ]
            );
            $outArray[$label] = [
                $config[0],
                $config[1],
                !$config[2],
            ];
        }
        return $outArray;
    }

    public function provideDataForLessThanEquals()
    {
        $array = $this->provideDataForGreaterThan();
        $outArray = [];

        foreach ($array as $label => $config) {
            $label = strtr(
                $label,
                [
                    '&gt;' => '&lt;=',
                    'evaluates true' => 'evaluates false',
                    'evaluates false' => 'evaluates true',
                ]
            );
            $outArray[$label] = [
                $config[0],
                $config[1],
                !$config[2],
            ];
        }
        return $outArray;
    }

    public function provideDataForNotEquals()
    {
        $array = $this->provideDataForEquals();
        $outArray = [];

        foreach ($array as $label => $config) {
            $label = strtr(
                $label,
                [
                    '==' => '!=',
                    'evaluates true' => 'evaluates false',
                    'evaluates false' => 'evaluates true',
                ]
            );
            $outArray[$label] = [
                $config[0],
                $config[1],
                !$config[2],
            ];
        }
        return $outArray;
    }

    public function provideDataForNotSame()
    {
        $array = $this->provideDataForSame();
        $outArray = [];

        foreach ($array as $label => $config) {
            $label = strtr(
                $label,
                [
                    '===' => '!==',
                    'evaluates true' => 'evaluates false',
                    'evaluates false' => 'evaluates true',
                ]
            );
            $outArray[$label] = [
                $config[0],
                $config[1],
                !$config[2],
            ];
        }
        return $outArray;
    }

    public function provideDataForSame()
    {
        $array = [
            [
                true,
                true,
                true,
            ],
            [
                true,
                42,
                false,
            ],
            [
                true,
                '42',
                false,
            ],
            [
                true,
                42.00,
                false,
            ],
            [
                false,
                0,
                false,
            ],
            [
                false,
                '',
                false,
            ],
            [
                42,
                42,
                true,
            ],
            [
                42,
                '42',
                false,
            ],
            [
                42,
                42.00,
                false,
            ],
            [
                '42',
                42.00,
                false,
            ],

            [
                false,
                true,
                false,
            ],
            [
                false,
                42,
                false,
            ],
            [
                false,
                '42',
                false,
            ],
            [
                false,
                42.00,
                false,
            ],
            [
                4,
                2,
                false,
            ],
            [
                42,
                'foobar',
                false,
            ],
            [
                42,
                42.0001,
                false,
            ],
            [
                'foobar',
                42.00,
                false,
            ],
        ];

        $labels = [];
        foreach ($array as $value) {
            $labels[] = sprintf(
                '%s === %s evaluates %s',
                $this->renderValue($value[0]),
                $this->renderValue($value[1]),
                $value[2] ? 'true' : 'false'
            );
        }
        return array_combine($labels, $array);
    }

    /**
     * @dataProvider provideDataForEquals
     */
    public function testEquals($value1, $value2, $expected)
    {
        $this->initArguments($value1, $value2, '=');
        self::assertSame($expected, $this->viewHelper->render());

        $this->initArguments($value1, $value2, '==');
        self::assertSame($expected, $this->viewHelper->render());
    }

    /**
     * @dataProvider provideDataForGreaterThan
     */
    public function testGreaterThan($value1, $value2, $expected)
    {
        $this->initArguments($value1, $value2, '>');
        self::assertSame($expected, $this->viewHelper->render());
    }

    /**
     * @dataProvider provideDataForGreaterThanEquals
     */
    public function testGreaterThanEquals($value1, $value2, $expected)
    {
        $this->initArguments($value1, $value2, '>=');
        self::assertSame($expected, $this->viewHelper->render());

        $this->initArguments($value1, $value2, '=>');
        self::assertSame($expected, $this->viewHelper->render());
    }

    /**
     * @dataProvider provideDataForLessThan
     */
    public function testLessThan($value1, $value2, $expected)
    {
        $this->initArguments($value1, $value2, '<');
        self::assertSame($expected, $this->viewHelper->render());
    }

    /**
     * @dataProvider provideDataForLessThanEquals
     */
    public function testLessThanEquals($value1, $value2, $expected)
    {
        $this->initArguments($value1, $value2, '<=');
        self::assertSame($expected, $this->viewHelper->render());
    }

    /**
     * @dataProvider provideDataForNotEquals
     */
    public function testNotEquals($value1, $value2, $expected)
    {
        $this->initArguments($value1, $value2, '!=');
        self::assertSame($expected, $this->viewHelper->render());

        $this->initArguments($value1, $value2, '<>');
        self::assertSame($expected, $this->viewHelper->render());
    }

    /**
     * @dataProvider provideDataForNotSame
     */
    public function testNotSame($value1, $value2, $expected)
    {
        $this->initArguments($value1, $value2, '!==');
        self::assertSame($expected, $this->viewHelper->render());
    }

    /**
     * @dataProvider provideDataForSame
     */
    public function testSame($value1, $value2, $expected)
    {
        $this->initArguments($value1, $value2, '===');
        self::assertSame($expected, $this->viewHelper->render());
    }

    protected function initViewHelper()
    {
        $this->viewHelper = new CompareViewHelper();
    }

    /**
     * helps rendering a value and its type for atomic values
     *
     * @param mixed $value
     * @return string
     */
    protected function renderValue($value)
    {
        if (is_bool($value)) {
            return sprintf('boolean: %b', $value);
        } elseif (is_integer($value)) {
            return sprintf('integer: %d', $value);
        } elseif (is_float($value)) {
            return sprintf('integer: %f', $value);
        } elseif (is_string($value)) {
            return sprintf('string: "%s"', $value);
        }

        throw new InvalidArgumentException('Unknown variable type!');
    }
}
