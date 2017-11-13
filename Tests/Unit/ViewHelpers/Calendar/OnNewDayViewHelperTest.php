<?php

namespace Tx\CzSimpleCal\Tests\Unit\ViewHelper\Calendar;

use Tx\CzSimpleCal\Domain\Model\EventIndex;
use Tx\CzSimpleCal\Tests\Unit\ViewHelpers\Calendar\Mocks\OnNewDayViewHelperMock;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\CMS\Fluid\Core\ViewHelper\ViewHelperVariableContainer;
use TYPO3\CMS\Fluid\Tests\Unit\ViewHelpers\ViewHelperBaseTestcase;

/**
 * testing the features of the Calendar_OnNewDayViewHelper
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class OnNewDayViewHelperTest extends ViewHelperBaseTestcase
{
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

        $this->viewHelper->render($model);

        self::assertSame('', $this->viewHelper->render($model));
    }

    public function testIfContentIsRenderedIfLastViewHelperWasOnEarlierDay()
    {
        $model = new EventIndex();
        $model->setStart(1234567890);
        $model->setEnd(1234567890);

        $this->viewHelper->render($model);

        $model = new EventIndex();
        $model->setStart(1234567890 + 86400);
        $model->setEnd(1234567890 + 86400);

        self::assertSame('tag content', $this->viewHelper->render($model));
    }

    public function testIfContentIsRenderedIfNoViewHelperWasPreviouslyUsed()
    {
        $model = new EventIndex();
        $model->setStart(1234567890);
        $model->setEnd(1234567890);

        self::assertSame('tag content', $this->viewHelper->render($model));
    }

    public function testMultipleIrelatedInstances()
    {
        $model = new EventIndex();
        $model->setStart(1234567890);
        $model->setEnd(1234567890);

        $this->viewHelper->render($model);

        self::assertSame('tag content', $this->viewHelper->render($model, 'foobar'));
    }

    protected function initViewHelper()
    {
        $this->viewHelper = new OnNewDayViewHelperMock();

        $this->viewHelperVariableContainer = new ViewHelperVariableContainer();

        $renderingContext = new RenderingContext();
        $renderingContext->injectViewHelperVariableContainer($this->viewHelperVariableContainer);

        $this->viewHelper->setRenderingContext($renderingContext);
    }
}
