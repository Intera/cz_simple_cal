<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Tests\Unit\ViewHelper\Calendar;

use Tx\CzSimpleCal\Tests\Unit\ViewHelpers\IndexedArgumentsTrait;
use Tx\CzSimpleCal\Utility\DateTime;
use Tx\CzSimpleCal\ViewHelpers\Calendar\CreateDateTimeViewHelper;
use TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase;

/**
 * testing the features of the Calendar_CreateDateViewHelper
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class CreateDateTimeViewHelperTest extends ViewHelperBaseTestcase
{
    use IndexedArgumentsTrait;

    /**
     * @var CreateDateTimeViewHelper
     */
    protected $viewHelper = null;

    protected $viewHelperNode = null;

    protected $viewHelperVariableContainer = null;

    public function setUp()
    {
        parent::setUp();

        $this->initViewHelper();
    }

    public function testBasic()
    {
        $this->initArguments('2009-02-13 23:31:30GMT');
        $dateTime = $this->viewHelper->render();

        self::assertTrue(
            $dateTime instanceof DateTime,
            'returned object is an instance of Tx_CzSimpleCal_Utility_DateTime'
        );
        self::assertEquals(1234567890, $dateTime->format('U'), 'object with correct time was created');
    }

    protected function initViewHelper()
    {
        $this->viewHelper = new CreateDateTimeViewHelper();
    }
}
