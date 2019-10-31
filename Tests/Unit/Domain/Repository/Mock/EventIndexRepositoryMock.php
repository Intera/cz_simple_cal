<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Tests\Unit\Domain\Repository\Mock;

use Tx\CzSimpleCal\Domain\Repository\EventIndexRepository;

class EventIndexRepositoryMock extends EventIndexRepository
{
    public function cleanSettings($settings)
    {
        return parent::cleanSettings($settings);
    }
}
