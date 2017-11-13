<?php

namespace Tx\CzSimpleCal\Tests\Unit\ViewHelper\Format;

use Tx\CzSimpleCal\Tests\Unit\ViewHelpers\Format\Mocks\TimespanToWordsViewHelperMock;
use Tx\CzSimpleCal\Utility\DateTime;
use Tx\CzSimpleCal\ViewHelpers\Format\TimespanToWordsViewHelper;
use TYPO3\CMS\Fluid\Tests\Unit\ViewHelpers\ViewHelperBaseTestcase;

/**
 * testing the features of the Format_TimespanToWordsViewHelper
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class TimespanToWordsViewHelperTest extends ViewHelperBaseTestcase
{
    /**
     * @var TimespanToWordsViewHelper
     */
    protected $viewHelper = null;

    public function setUp()
    {
        parent::setUp();
        $this->viewHelper = new TimespanToWordsViewHelperMock();
    }

    public function testElse()
    {
        self::assertEquals(
            'from February 13, 2009 to March 15, 2010',
            $this->viewHelper->render(
                new DateTime('2009-02-13'),
                new DateTime('2010-03-15')
            )
        );
    }

    public function testOnOneDay()
    {
        self::assertEquals(
            'on February 13, 2009',
            $this->viewHelper->render(
                new DateTime('2009-02-13'),
                new DateTime('2009-02-13')
            )
        );
    }

    public function testOnOneMonth()
    {
        self::assertEquals(
            'from February 13 to 15, 2009',
            $this->viewHelper->render(
                new DateTime('2009-02-13'),
                new DateTime('2009-02-15')
            )
        );
    }

    public function testOnOneYear()
    {
        self::assertEquals(
            'from February 13 to March 15, 2009',
            $this->viewHelper->render(
                new DateTime('2009-02-13'),
                new DateTime('2009-03-15')
            )
        );
    }
}
