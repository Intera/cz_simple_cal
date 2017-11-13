<?php

namespace Tx\CzSimpleCal\ViewHelpers\Format;

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
 * Renders a readable version for a timespan for days with as little
 * repetition as possible.
 *
 * <code>
 *   <cal:format.timespan start="{christmasEve2010}" end="{newYearsEve2010}" />
 * </code>
 *
 * outputs "Dec 24 to 31, 2010"
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class TimespanToWordsViewHelper extends AbstractViewHelper
{
    /**
     * the name of the extension that uses this ViewHelper
     * (used to determine the correct translation file)
     *
     * @var string
     */
    protected $extensionName = null;

    /**
     * Render the supplied unix \Tx\CzSimpleCal\Utility\DateTime in a localized human-readable string.
     *
     * @param \Tx\CzSimpleCal\Utility\DateTime $start
     * @param \Tx\CzSimpleCal\Utility\DateTime $end
     * @return string formatted output
     * @author Christian Zenker <christian.zenker@599media.de>
     */
    public function render($start, $end)
    {
        if ($start->format('Y') != $end->format('Y')) {
            return
                $this->getLL('timespan.from') . ' ' .
                strftime($this->getLL('timespan.format.else.start'), $start->getTimestamp()) . ' ' .
                $this->getLL('timespan.to') . ' ' .
                strftime($this->getLL('timespan.format.else.end'), $end->getTimestamp());
        } elseif ($start->format('m') != $end->format('m')) {
            return
                $this->getLL('timespan.from') . ' ' .
                strftime($this->getLL('timespan.format.sameYear.start'), $start->getTimestamp()) . ' ' .
                $this->getLL('timespan.to') . ' ' .
                strftime($this->getLL('timespan.format.sameYear.end'), $end->getTimestamp());
        } elseif ($start->format('d') != $end->format('d')) {
            return
                $this->getLL('timespan.from') . ' ' .
                strftime($this->getLL('timespan.format.sameMonth.start'), $start->getTimestamp()) . ' ' .
                $this->getLL('timespan.to') . ' ' .
                strftime($this->getLL('timespan.format.sameMonth.end'), $end->getTimestamp());
        } else {
            return
                $this->getLL('timespan.on') . ' ' .
                strftime($this->getLL('timespan.format.sameDay'), $start->getTimestamp());
        }
    }

    /**
     * helping function to get a translation of a string
     *
     * @param string $key
     * @return string
     */
    protected function getLL($key)
    {
        if (is_null($this->extensionName)) {
            $this->extensionName = $this->controllerContext->getRequest()->getControllerExtensionName();
        }
        return \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($key, $this->extensionName);
    }
}
