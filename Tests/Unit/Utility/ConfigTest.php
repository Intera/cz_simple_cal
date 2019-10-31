<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Tests\Unit\Utility;

use Tx\CzSimpleCal\Utility\Config;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * testing the Config class
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class ConfigTest extends UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['cz_simple_cal'] = ['foo' => 'bar'];
    }

    protected function tearDown()
    {
        parent::tearDown();

        unset($GLOBALS['TYPO3_CONF_VARS']);
    }

    public function testInitialization()
    {
        self::assertTrue(Config::exists('foo'), 'field is existant');
        self::assertEquals('bar', Config::get('foo'), 'field has correct value');
    }

    public function testSetter()
    {
        Config::set('foo', 'baz');
        self::assertEquals('baz', Config::get('foo'), 'setting of existant values works');

        Config::set('baz', 'foo');
        self::assertEquals('foo', Config::get('baz'), 'setting of non-existant values works');

        Config::set(['hello' => 'world']);
        self::assertEquals('world', Config::get('hello'), 'setting of an array');
    }
}
