<?php

namespace Tx\CzSimpleCal\ViewHelpers\Arrays;

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
 * Join item view helper.
 */
class JoinItemViewHelper extends AbstractViewHelper
{
    /**
     * @throws \LogicException
     * @return void
     */
    public function render()
    {
        $viewHelperName = str_replace('_JoinItemViewHelper', '_JoinViewHelper', get_class($this));
        $key = 'items';
        if (!$this->viewHelperVariableContainer->exists($viewHelperName, $key)) {
            throw new \LogicException(sprintf('%s must be used as child of %s.', get_class($this), $viewHelperName));
        }

        $values = $this->viewHelperVariableContainer->get($viewHelperName, $key);
        $values[] = $this->renderChildren();

        $this->viewHelperVariableContainer->addOrUpdate($viewHelperName, $key, $values);
    }
}
