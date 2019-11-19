<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\ViewHelpers\Calendar;

use TYPO3\CMS\Fluid\ViewHelpers\Form\SelectViewHelper;

class YearSelectViewHelper extends SelectViewHelper
{
    protected function getOptions()
    {
        $options = ['' => 'Ab heute'];

        $firstYear = date('Y') - 2;
        $lastYear = $firstYear + 5;
        $currentYear = $firstYear;
        while ($currentYear < $lastYear) {
            $options[$currentYear] = $currentYear;
        }

        return $options;
    }
}
