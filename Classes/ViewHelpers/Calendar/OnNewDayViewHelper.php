<?php

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

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

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
     *
     * @param \Tx\CzSimpleCal\Domain\Model\EventIndex $event the event to compare to the previously submitted one
     * @param string $label if you need multiple irrelated instances set this to something unique
     * @return string
     */
    public function render($event, $label = '')
    {
        $className = get_class($this);

        $name = 'last_day_wrapper_date';
        if ($label) {
            $name .= '_' . $label;
        }

        $lastDay = null;
        if ($this->viewHelperVariableContainer->exists($className, $name)) {
            $lastDay = $this->viewHelperVariableContainer->get($className, $name);
        }

        $thisDay = strtotime('midnight', $event->getStart());

        if ($thisDay == $lastDay) {
            return '';
        } else {
            $this->viewHelperVariableContainer->addOrUpdate($className, $name, $thisDay);
            return $this->renderChildren();
        }
    }
}
