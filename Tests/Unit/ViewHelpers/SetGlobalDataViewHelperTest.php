<?php

namespace Tx\CzSimpleCal\Tests\Unit\ViewHelper;

use Tx\CzSimpleCal\ViewHelpers\SetGlobalDataViewHelper;
use TYPO3\CMS\Fluid\Tests\Unit\ViewHelpers\ViewHelperBaseTestcase;

/**
 * testing the features of the SetGlobalDataViewHelper
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class SetGlobalDataViewHelperTest extends ViewHelperBaseTestcase
{
    protected $oldGlobalData = null;

    protected $viewHelper = null;

    public function setUp()
    {
        parent::setUp();
        $this->viewHelper = new SetGlobalDataViewHelper();
        $this->oldGlobalData = $GLOBALS['TSFE']->cObj->data;
    }

    public function tearDown()
    {
        $GLOBALS['TSFE']->cObj->data = $this->oldGlobalData;
    }

    public function testBasic()
    {
        $this->viewHelper->render('foo', 'bar');
        self::assertEquals('bar', $GLOBALS['TSFE']->cObj->data['foo'], 'non-existant field is set');

        $this->viewHelper->render('foo', 'baz');
        self::assertEquals('baz', $GLOBALS['TSFE']->cObj->data['foo'], 'existant field is overriden');
    }
}
