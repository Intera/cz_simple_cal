<?php

namespace Tx\CzSimpleCal\Tests\Unit\Utility;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Tx\CzSimpleCal\Utility\Config;

/**
 * testing the Config class
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class ConfigTest extends UnitTestCase
{
    /**
     * stores the GLOBALS array
     *
     * @var array
     */
    protected $oldGlobals = null;

    public function setUp()
    {
        $this->oldGlobals = $GLOBALS;
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['cz_simple_cal'] = serialize(['foo' => 'bar']);
    }

    public function tearDown()
    {
        $GLOBALS = $this->oldGlobals;
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
