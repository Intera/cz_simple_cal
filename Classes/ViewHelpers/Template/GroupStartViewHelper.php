<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\ViewHelpers\Template;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class GroupStartViewHelper extends AbstractTagBasedViewHelper
{
    public function initializeArguments()
    {
        $this->registerUniversalTagAttributes();
    }

    public function render()
    {
        $this->viewHelperVariableContainer->addOrUpdate(__CLASS__, 'groupStarted', true);

        $tag = $this->tag->render();
        $tagWithoutClosing = str_replace('</div>', '', $tag);
        return $tagWithoutClosing;
    }
}
