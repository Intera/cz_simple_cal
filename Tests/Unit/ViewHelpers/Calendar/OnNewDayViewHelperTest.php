<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Tests\Unit\ViewHelper\Calendar;

use Tx\CzSimpleCal\Domain\Model\EventIndex;
use Tx\CzSimpleCal\Tests\Unit\ViewHelpers\Calendar\Mocks\OnNewDayViewHelperMock;
use Tx\CzSimpleCal\Tests\Unit\ViewHelpers\IndexedArgumentsTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperVariableContainer;

/**
 * testing the features of the Calendar_OnNewDayViewHelper
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class OnNewDayViewHelperTest extends ViewHelperBaseTestcase
{
    use IndexedArgumentsTrait;

    /**
     * @var OnNewDayViewHelperMock
     */
    protected $viewHelper = null;

    protected $viewHelperVariableContainer = null;

    public function setUp()
    {
        parent::setUp();

        $this->initViewHelper();
    }

    public function testIfContentIsNotRenderedIfLastViewHelperWasOnSameDay()
    {
        $model = new EventIndex();
        $model->setStart(1234567890);
        $model->setEnd(1234567890);

        $this->initArguments($model);
        $this->viewHelper->render();

        $this->initArguments($model);
        self::assertSame('', $this->viewHelper->render());
    }

    public function testIfContentIsRenderedIfLastViewHelperWasOnEarlierDay()
    {
        $model = new EventIndex();
        $model->setStart(1234567890);
        $model->setEnd(1234567890);

        $this->initArguments($model);
        $this->viewHelper->render();

        $model = new EventIndex();
        $model->setStart(1234567890 + 86400);
        $model->setEnd(1234567890 + 86400);

        $this->initArguments($model);
        self::assertSame('tag content', $this->viewHelper->render());
    }

    public function testIfContentIsRenderedIfNoViewHelperWasPreviouslyUsed()
    {
        $model = new EventIndex();
        $model->setStart(1234567890);
        $model->setEnd(1234567890);

        $this->initArguments($model);
        self::assertSame('tag content', $this->viewHelper->render());
    }

    public function testMultipleIrelatedInstances()
    {
        $model = new EventIndex();
        $model->setStart(1234567890);
        $model->setEnd(1234567890);

        $this->initArguments($model);
        $this->viewHelper->render();

        $this->initArguments($model, 'foobar');
        self::assertSame('tag content', $this->viewHelper->render());
    }

    protected function initViewHelper()
    {
        $this->viewHelper = new OnNewDayViewHelperMock();
        $this->injectDependenciesIntoViewHelper($this->viewHelper);

        // We need to use a real instance of the ViewHelperVariableContainer!
        /** @noinspection PhpUndefinedMethodInspection */
        $this->renderingContext->_set(
            'viewHelperVariableContainer',
            GeneralUtility::makeInstance(ViewHelperVariableContainer::class)
        );
        $this->viewHelper->setRenderingContext($this->renderingContext);
    }
}
