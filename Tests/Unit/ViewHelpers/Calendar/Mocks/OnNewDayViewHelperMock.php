<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Tests\Unit\ViewHelpers\Calendar\Mocks;

use Tx\CzSimpleCal\ViewHelpers\Calendar\OnNewDayViewHelper;

class OnNewDayViewHelperMock extends OnNewDayViewHelper
{
    public function renderChildren()
    {
        return 'tag content';
    }
}
