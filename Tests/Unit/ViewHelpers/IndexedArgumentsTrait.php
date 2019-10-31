<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Tests\Unit\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * @property AbstractViewHelper $viewHelper
 */
trait IndexedArgumentsTrait
{
    private function initArguments(...$argumentList)
    {
        $mappedArguments = [];
        $argumentDefinitions = $this->viewHelper->prepareArguments();
        foreach ($argumentList as $argumentValue) {
            $currentArgument = array_shift($argumentDefinitions);
            $argumentName = $currentArgument->getName();
            $mappedArguments[$argumentName] = $argumentValue;
        }
        $this->setArgumentsUnderTest($this->viewHelper, $mappedArguments);
    }
}
