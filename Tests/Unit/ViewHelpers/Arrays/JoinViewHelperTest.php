<?php

namespace Tx\CzSimpleCal\Tests\Unit\ViewHelper\Arrays;

use Tx\CzSimpleCal\ViewHelpers\Arrays\JoinViewHelper;
use TYPO3\CMS\Fluid\Tests\Unit\ViewHelpers\ViewHelperBaseTestcase;

/**
 * testing the features of the Array_JoinViewHelper
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class JoinViewHelperTest extends ViewHelperBaseTestcase
{
    protected $viewHelper = null;

    public function setUp()
    {
        parent::setUp();
        $this->viewHelper = new JoinViewHelper();
    }

    public function testBasic()
    {
        self::assertEquals('foo, bar', $this->viewHelper->render(['foo', 'bar']), 'default imploder is ", "');
    }

    public function testByParameter()
    {
        self::assertEquals('foo#bar', $this->viewHelper->render(['foo', 'bar'], '#'), '"by" parameter is recognized');
    }

    public function testRemoveEmptyParameter()
    {
        self::assertEquals(
            'foo##bar',
            $this->viewHelper->render(['foo', '', 'bar'], '#'),
            'empty values are not removed by default'
        );
        self::assertEquals(
            'foo#bar',
            $this->viewHelper->render(['foo', '', 'bar'], '#', true),
            'empty values can be removed'
        );
    }
}
