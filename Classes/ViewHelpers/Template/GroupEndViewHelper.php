<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\ViewHelpers\Template;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class GroupEndViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    public function render()
    {
        $result = '';
        if ($this->viewHelperVariableContainer->exists(GroupStartViewHelper::class, 'groupStarted')) {
            $result = '</div>';
        }

        $this->viewHelperVariableContainer->remove(GroupStartViewHelper::class, 'groupStarted');

        return $result;
    }
}
