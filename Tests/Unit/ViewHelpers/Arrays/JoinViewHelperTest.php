<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Tests\Unit\ViewHelper\Arrays;

use Tx\CzSimpleCal\Tests\Unit\ViewHelpers\IndexedArgumentsTrait;
use Tx\CzSimpleCal\ViewHelpers\Arrays\JoinViewHelper;
use TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase;

/**
 * testing the features of the Array_JoinViewHelper
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class JoinViewHelperTest extends ViewHelperBaseTestcase
{
    use IndexedArgumentsTrait;

    protected $viewHelper = null;

    public function setUp()
    {
        parent::setUp();

        $this->viewHelper = new JoinViewHelper();
    }

    public function testBasic()
    {
        $this->initArguments(['foo', 'bar']);
        self::assertEquals('foo, bar', $this->viewHelper->render(), 'default imploder is ", "');
    }

    public function testByParameter()
    {
        $this->initArguments(['foo', 'bar'], '#');
        self::assertEquals('foo#bar', $this->viewHelper->render(), '"by" parameter is recognized');
    }

    public function testRemoveEmptyParameter()
    {
        $this->initArguments(['foo', '', 'bar'], '#');
        self::assertEquals(
            'foo##bar',
            $this->viewHelper->render(),
            'empty values are not removed by default'
        );

        $this->initArguments(['foo', '', 'bar'], '#', true);
        self::assertEquals(
            'foo#bar',
            $this->viewHelper->render(),
            'empty values can be removed'
        );
    }
}
