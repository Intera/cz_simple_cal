<?php
declare(strict_types=1);

use Tx\CzSimpleCal\Command\IndexEventsCommand;

return [
    'cz_simple_cal:index' => [
        'class' => IndexEventsCommand::class,
        'schedulable' => true,
    ],
];
