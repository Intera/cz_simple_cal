<?php

namespace Tx\CzSimpleCal\ViewHelpers\Format;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "cz_simple_cal".            *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Converts a string to lowercase.
 */
class StrToLowerViewHelper extends AbstractViewHelper
{
    /**
     * Converts the rendered children to lowercase.
     *
     * @return string
     */
    public function render()
    {
        $data = $this->renderChildren();
        return strtolower($data);
    }
}
