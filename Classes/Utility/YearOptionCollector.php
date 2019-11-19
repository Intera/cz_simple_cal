<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Utility;

use TYPO3\CMS\Core\SingletonInterface;

class YearOptionCollector implements SingletonInterface
{
    public function buildYearOptions(): array
    {
        $options = [];

        $firstYear = date('Y') - 2;
        $lastYear = $firstYear + 5;
        $currentYear = $firstYear;
        while ($currentYear < $lastYear) {
            $options[] = $currentYear;
            $currentYear++;
        }

        return $options;
    }
}
