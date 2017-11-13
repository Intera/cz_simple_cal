<?php

namespace Tx\CzSimpleCal\Tests\Unit\ViewHelper\Format;

use Tx\CzSimpleCal\ViewHelpers\Format\NumberChoiceViewHelper;
use TYPO3\CMS\Fluid\Tests\Unit\ViewHelpers\ViewHelperBaseTestcase;

/**
 * testing the features of the Condition_CompareViewHelper
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class NumberChoiceViewHelperTest extends ViewHelperBaseTestcase
{
    /**
     * @var NumberChoiceViewHelper
     */
    protected $viewHelper = null;

    public function setUp()
    {
        parent::setUp();
        $this->viewHelper = new NumberChoiceViewHelper();
    }

    public function provider()
    {
        return [
            [
                '[0]foo',
                0,
                'foo',
            ],
            [
                '[0]foo|[1]bar',
                1,
                'bar',
            ],
            // +Inf
            [
                '[0,+Inf]foo',
                0,
                'foo',
            ],
            [
                '(0,+Inf]foo|[0]bar',
                0,
                'bar',
            ],
            // Large numbers
            [
                '[0]bar|(0,+Inf]foo',
                9999,
                'foo',
            ],
            // -Inf
            [
                '[-Inf,0]foo|(0,+Inf]bar',
                -3,
                'foo',
            ],
            // Overlapping intervals
            [
                '[42]foo|[0,+Inf]bar',
                42,
                'foo',
            ],
        ];
    }

    public function testArgumentsParameter()
    {
        self::assertEquals(
            'baz',
            $this->viewHelper->render(1, '[0]foo|[1]###bar###', ['bar' => 'baz']),
            'markers are substituted.'
        );
    }

    /**
     * @dataProvider provider
     */
    public function testIfCorrectIntervalIsFound($text, $number, $assert)
    {
        self::assertEquals($assert, $this->viewHelper->render($number, $text));
    }
}
