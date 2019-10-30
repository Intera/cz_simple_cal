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

use Tx\CzSimpleCal\Utility\DateTime;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * create a \Tx\CzSimpleCal\Utility\DateTime object
 *
 * Usage example
 * <example>
 *   <f:map alias="foo:{cal:calendar.dateTime(dateTime:'now')}">
 *     <f:debug>{foo}</f:debug>
 *   </f:map>
 * </example>
 */
class CreateDateTimeViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        $this->registerArgument('dateTime', DateTime::class, 'some string of the type date', true);
    }

    public function render(): DateTime
    {
        return new DateTime($this->arguments['dateTime']);
    }
}
