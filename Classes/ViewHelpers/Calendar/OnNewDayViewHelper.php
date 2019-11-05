<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\ViewHelpers\Calendar;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Christian Zenker <christian.zenker@599media.de>, 599media GmbH
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use InvalidArgumentException;
use Tx\CzSimpleCal\Domain\Model\EventIndex;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * renders its content if the submitted event is on a different date then the previous one
 *
 * <example>
 * <f:for each="{events}" as="event">
 *   <cal:calendar.onNewDay event="{event}">
 *     Good morning. This is a new day.
 *   </cal:calendar.onNewDay>
 *   {event.title}
 * </f:for>
 * </example>
 */
class OnNewDayViewHelper extends AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeChildren = false;

    /**
     * @var bool
     */
    protected $escapeOutput = false;

    public function initializeArguments()
    {
        $this->registerArgument(
            'event',
            EventIndex::class,
            'the event to compare to the previously submitted one',
            true
        );
        $this->registerArgument(
            'label',
            'string',
            'if you need multiple irrelated instances set this to something unique',
            false,
            ''
        );
        $this->registerArgument(
            'mode',
            'string',
            'Can be used to check if event is on a different month by passing "month"',
            false,
            'day'
        );
    }

    public function render(): string
    {
        /** @var EventIndex $event */
        $event = $this->arguments['event'];
        $label = $this->arguments['label'];

        $className = get_class($this);

        $variableName = 'last_day_wrapper_date';
        if ($label) {
            $variableName .= '_' . $label;
        }

        $lastValue = null;
        if ($this->viewHelperVariableContainer->exists($className, $variableName)) {
            $lastValue = $this->viewHelperVariableContainer->get($className, $variableName);
        }

        $currentValue = $this->getCompareValue($event);
        if ($currentValue === $lastValue) {
            return '';
        }

        $this->viewHelperVariableContainer->addOrUpdate($className, $variableName, $currentValue);
        return $this->renderChildren();
    }

    protected function getCompareValue(EventIndex $event): string
    {
        $mode = $this->arguments['mode'];
        switch ($mode) {
            case 'day':
                return (string)strtotime('midnight', $event->getStart());
                break;
            case 'month':
                return date('n', $event->getStart());
                break;
            default:
                throw new InvalidArgumentException('Invalid mode: ' . $mode);
        }
    }
}
