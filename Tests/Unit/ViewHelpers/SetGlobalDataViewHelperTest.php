<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Tests\Unit\ViewHelper;

use stdClass;
use Tx\CzSimpleCal\Tests\Unit\ViewHelpers\IndexedArgumentsTrait;
use Tx\CzSimpleCal\ViewHelpers\SetGlobalDataViewHelper;
use TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase;

/**
 * testing the features of the SetGlobalDataViewHelper
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class SetGlobalDataViewHelperTest extends ViewHelperBaseTestcase
{
    use IndexedArgumentsTrait;

    protected $oldGlobalData = null;

    protected $viewHelper = null;

    public function setUp()
    {
        parent::setUp();
        $this->viewHelper = new SetGlobalDataViewHelper();

        $GLOBALS['TSFE'] = new stdClass();
        $GLOBALS['TSFE']->cObj = new stdClass();
        $GLOBALS['TSFE']->cObj->data = [];
    }

    public function tearDown()
    {
        parent::tearDown();

        unset($GLOBALS['TSFE']);
    }

    public function testBasic()
    {
        $this->initArguments('foo', 'bar');
        $this->viewHelper->render();
        self::assertEquals('bar', $GLOBALS['TSFE']->cObj->data['foo'], 'non-existant field is set');

        $this->initArguments('foo', 'baz');
        $this->viewHelper->render();
        self::assertEquals('baz', $GLOBALS['TSFE']->cObj->data['foo'], 'existant field is overriden');
    }
}
