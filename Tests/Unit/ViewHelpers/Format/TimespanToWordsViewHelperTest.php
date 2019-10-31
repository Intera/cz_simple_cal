<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Tests\Unit\ViewHelper\Format;

use Tx\CzSimpleCal\Tests\Unit\ViewHelpers\Format\Mocks\TimespanToWordsViewHelperMock;
use Tx\CzSimpleCal\Tests\Unit\ViewHelpers\IndexedArgumentsTrait;
use Tx\CzSimpleCal\Utility\DateTime;
use Tx\CzSimpleCal\ViewHelpers\Format\TimespanToWordsViewHelper;
use TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase;

/**
 * testing the features of the Format_TimespanToWordsViewHelper
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class TimespanToWordsViewHelperTest extends ViewHelperBaseTestcase
{
    use IndexedArgumentsTrait;

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
        $this->initArguments(
            new DateTime('2009-02-13'),
            new DateTime('2010-03-15')
        );
        self::assertEquals(
            'from February 13, 2009 to March 15, 2010',
            $this->viewHelper->render()
        );
    }

    public function testOnOneDay()
    {
        $this->initArguments(
            new DateTime('2009-02-13'),
            new DateTime('2009-02-13')
        );
        self::assertEquals(
            'on February 13, 2009',
            $this->viewHelper->render()
        );
    }

    public function testOnOneMonth()
    {
        $this->initArguments(
            new DateTime('2009-02-13'),
            new DateTime('2009-02-15')
        );
        self::assertEquals(
            'from February 13 to 15, 2009',
            $this->viewHelper->render()
        );
    }

    public function testOnOneYear()
    {
        $this->initArguments(
            new DateTime('2009-02-13'),
            new DateTime('2009-03-15')
        );
        self::assertEquals(
            'from February 13 to March 15, 2009',
            $this->viewHelper->render()
        );
    }
}
