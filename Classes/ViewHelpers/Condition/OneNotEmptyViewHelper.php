<?php

namespace Tx\CzSimpleCal\ViewHelpers\Condition;

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
 * A view helper to return true if one of the values is not empty
 */
class OneNotEmptyViewHelper extends AbstractViewHelper
{
    /**
     * @param array $values the values
     * @return boolean if the condition is met
     * @author Christian Zenker <christian.zenker@599media.de>
     */
    public function render($values)
    {
        foreach ($values as $value) {
            if (!empty($value)) {
                return true;
            }
        }
        return false;
    }
}
