<?php

namespace Tx\CzSimpleCal\Tests\Unit\ViewHelpers\Format\Mocks;

use Tx\CzSimpleCal\ViewHelpers\Format\TimespanToWordsViewHelper;

class TimespanToWordsViewHelperMock extends TimespanToWordsViewHelper
{
    protected $ll = [
        'timespan.format.sameDay' => '%B %e, %Y',
        'timespan.format.sameMonth.start' => '%B %e',
        'timespan.format.sameMonth.end' => '%e, %Y',
        'timespan.format.sameYear.start' => '%B %e',
        'timespan.format.sameYear.end' => '%B %e, %Y',
        'timespan.format.else.start' => '%B %e, %Y',
        'timespan.format.else.end' => '%B %e, %Y',
        'timespan.from' => 'from',
        'timespan.to' => 'to',
        'timespan.on' => 'on',
    ];

    protected function getLL($key)
    {
        return $this->ll[$key];
    }
}
