<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Tests\Unit\Recurrance\Mocks;

use Tx\CzSimpleCal\Recurrance\Timeline\Event;

class TimelineEventMock extends Event
{
    public function getData()
    {
        return $this->count() > 0 ?
            array_combine(
                array_flip(array_keys(array_fill(0, $this->count(), 'foo'))),
                $this->data
            ) :
            [];
    }
}
