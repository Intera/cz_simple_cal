<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\ViewHelpers\Template;

use DateTimeInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

class DateTagViewHelper extends AbstractTagBasedViewHelper
{
    public function initializeArguments()
    {
        $this->registerArgument('type', 'string', 'dtstart or tdend', true);
        $this->registerArgument('date', DateTimeInterface::class, 'The datetime that should be rendered', true);
        $this->registerUniversalTagAttributes();
    }

    public function render()
    {
        $this->tag->setTagName('span');
        $this->tag->forceClosingTag(true);

        $this->tag->setContent($this->renderChildren());

        $this->tag->addAttribute(
            'title',
            strftime('%Y-%m-%dT%H:%M%z', $this->getDate()->getTimestamp())
        );

        return $this->tag->render();
    }

    private function getDate(): DateTimeInterface
    {
        return $this->arguments['date'];
    }
}
