<?php

namespace Tx\CzSimpleCal\Tests\Unit\ViewHelper\Condition;

use Tx\CzSimpleCal\ViewHelpers\Condition\OneNotEmptyViewHelper;
use TYPO3\CMS\Fluid\Tests\Unit\ViewHelpers\ViewHelperBaseTestcase;

/**
 * testing the features of the Condition_OneNotEmptyViewHelper
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class OneNotEmptyViewHelperTest extends ViewHelperBaseTestcase
{
    /**
     * @var OneNotEmptyViewHelper
     */
    protected $viewHelper = null;

    protected $viewHelperNode = null;

    protected $viewHelperVariableContainer = null;

    public function setUp()
    {
        parent::setUp();

        $this->initViewHelper();
    }

    public function testComplexExamples()
    {
        self::assertSame(
            false,
            $this->viewHelper->render(
                [
                    '',
                    0,
                    false,
                ]
            ),
            'just empty values'
        );

        self::assertSame(
            true,
            $this->viewHelper->render(
                [
                    '',
                    0,
                    'foobar',
                ]
            ),
            'non-empty value as last item'
        );
        self::assertSame(
            true,
            $this->viewHelper->render(
                [
                    'foobar',
                    0,
                    false,
                ]
            ),
            'non-empty value as first item'
        );
        self::assertSame(
            true,
            $this->viewHelper->render(
                [
                    'foobar',
                    42,
                    true,
                ]
            ),
            'just non-empty values'
        );
    }

    public function testEmptyValues()
    {
        self::assertSame(false, $this->viewHelper->render([]), 'nothing at all');
        self::assertSame(false, $this->viewHelper->render(['']), 'empty string');
        self::assertSame(false, $this->viewHelper->render([0]), '0');
        self::assertSame(false, $this->viewHelper->render([false]), 'boolean false');
        self::assertSame(false, $this->viewHelper->render(['0']), 'string with a 0');
        self::assertSame(false, $this->viewHelper->render([[]]), 'empty array');
    }

    public function testNonEmptyValues()
    {
        self::assertSame(true, $this->viewHelper->render(['foobar']), 'non-empty string');
        self::assertSame(true, $this->viewHelper->render([42]), 'a positive integer');
        self::assertSame(true, $this->viewHelper->render([-42]), 'a negative integer');
        self::assertSame(true, $this->viewHelper->render([new \stdClass()]), 'a class');
        self::assertSame(true, $this->viewHelper->render([true]), 'boolean true');
    }

    protected function initViewHelper()
    {
        $this->viewHelper = new OneNotEmptyViewHelper();
    }
}
