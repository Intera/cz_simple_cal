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
 * join multiple values from an array into a string
 * (kind of PHP's implode())
 *
 * you might use the item property as well as the JoinItemViewHelper
 * to give the items to be joined
 *
 * <example>
 *   <cal:array.join items="{0:'foo', 1:'bar', 2:'baz'}" by=", " />
 *
 *   renders as
 *
 *   "foo, bar, baz"
 * </example>
 *
 * <example>
 *   <cal:array.join>
 *     <cal.array.joinItem>foo</cal.array.joinItem>
 *     <cal.array.joinItem>bar</cal.array.joinItem>
 *     <cal.array.joinItem>baz</cal.array.joinItem>
 *   </cal:array.join>
 *
 *   renders as
 *
 *   "foo, bar, baz"
 * </example>
 */
class JoinViewHelper extends AbstractViewHelper
{
    /**
     * @param array $items an array of strings that need to be joined
     * @param string $by the string used to glue the items together
     * @param boolean $removeEmpty if true, empty items will be removed
     * @return string Rendered result
     */
    public function render($items = null, $by = ', ', $removeEmpty = false)
    {
        if (is_null($items)) {
            $items = $this->getItems();
        }

        if ($removeEmpty) {
            $items = $this->removeEmpty($items);
        }

        return implode($by, $items);
    }

    /**
     * get items from the nodes
     *
     * @return array
     */
    protected function getItems()
    {
        $viewHelperName = get_class($this);
        $key = 'items';

        if ($this->viewHelperVariableContainer->exists($viewHelperName, $key)) {
            $temp = $this->viewHelperVariableContainer->get($viewHelperName, $key);
        }
        $this->viewHelperVariableContainer->addOrUpdate($viewHelperName, $key, []);

        $this->renderChildren();

        $return = $this->viewHelperVariableContainer->get($viewHelperName, $key);

        $this->viewHelperVariableContainer->remove($viewHelperName, $key);
        if (isset($temp)) {
            $this->viewHelperVariableContainer->add($viewHelperName, $key, $temp);
        }

        return $return;
    }

    /**
     * remove empty values from array
     *
     * @param array $items
     * @return array
     */
    protected function removeEmpty($items)
    {
        foreach ($items as $key => $value) {
            if (empty($value)) {
                unset($items[$key]);
            }
        }
        return $items;
    }
}
