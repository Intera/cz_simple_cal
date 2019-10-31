<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Tests\Unit\ViewHelper\Condition;

use stdClass;
use Tx\CzSimpleCal\Tests\Unit\ViewHelpers\IndexedArgumentsTrait;
use Tx\CzSimpleCal\ViewHelpers\Condition\OneNotEmptyViewHelper;
use TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase;

/**
 * testing the features of the Condition_OneNotEmptyViewHelper
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class OneNotEmptyViewHelperTest extends ViewHelperBaseTestcase
{
    use IndexedArgumentsTrait;

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
        $this->initArguments(
            [
                '',
                0,
                false,
            ]
        );
        self::assertSame(
            false,
            $this->viewHelper->render(),
            'just empty values'
        );

        $this->initArguments(
            [
                '',
                0,
                'foobar',
            ]
        );
        self::assertSame(
            true,
            $this->viewHelper->render(),
            'non-empty value as last item'
        );

        $this->initArguments(
            [
                'foobar',
                0,
                false,
            ]
        );
        self::assertSame(
            true,
            $this->viewHelper->render(),
            'non-empty value as first item'
        );

        $this->initArguments(
            [
                'foobar',
                42,
                true,
            ]
        );
        self::assertSame(
            true,
            $this->viewHelper->render(),
            'just non-empty values'
        );
    }

    public function testEmptyValues()
    {
        $this->initArguments([]);
        self::assertSame(false, $this->viewHelper->render(), 'nothing at all');

        $this->initArguments(['']);
        self::assertSame(false, $this->viewHelper->render(), 'empty string');

        $this->initArguments([0]);
        self::assertSame(false, $this->viewHelper->render(), '0');

        $this->initArguments([false]);
        self::assertSame(false, $this->viewHelper->render(), 'boolean false');

        $this->initArguments(['0']);
        self::assertSame(false, $this->viewHelper->render(), 'string with a 0');

        $this->initArguments([[]]);
        self::assertSame(false, $this->viewHelper->render(), 'empty array');
    }

    public function testNonEmptyValues()
    {
        $this->initArguments(['foobar']);
        self::assertSame(true, $this->viewHelper->render(), 'non-empty string');

        $this->initArguments([42]);
        self::assertSame(true, $this->viewHelper->render(), 'a positive integer');

        $this->initArguments([-42]);
        self::assertSame(true, $this->viewHelper->render(), 'a negative integer');

        $this->initArguments([new stdClass()]);
        self::assertSame(true, $this->viewHelper->render(), 'a class');

        $this->initArguments([true]);
        self::assertSame(true, $this->viewHelper->render(), 'boolean true');
    }

    protected function initViewHelper()
    {
        $this->viewHelper = new OneNotEmptyViewHelper();
    }
}
