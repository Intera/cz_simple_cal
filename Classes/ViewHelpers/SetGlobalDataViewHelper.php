<?php

namespace Tx\CzSimpleCal\ViewHelpers;

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
 * sets data of the global cObject
 *
 * you can utilize this to set the <title>-tag or other <head> data
 *
 * as page.headerData is generated after generating the content of the page
 * you can override some fields or create new ones
 *
 * <example>
 *   <code>
 *     <cal:setGlobalData field="title">{event.title}</cal:setGlobalData>
 *   </code>
 *
 *   <code>
 *     page.headerData {
 *       10 = TEXT
 *       10.field = title
 *       10.wrap = <title>|</title>
 *     }
 *   </code>
 *
 *   will output the events title as the pages title
 *
 * </example>
 *
 * or a less "hackier" example
 *
 * <example>
 *   <code>
 *     <cal:setGlobalData field="override_title" data="{event.title}" />
 *   </code>
 *
 *   <code>
 *     page.headerData {
 *       10 = TEXT
 *       10.field = override_title // title
 *       10.wrap = <title>|</title>
 *     }
 *   </code>
 * </example>
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class SetGlobalDataViewHelper extends AbstractViewHelper
{
    /**
     * @param string $field the field name to override or create
     * @param string $data the data to add to the field
     */
    public function render($field, $data = null)
    {
        if (is_null($data)) {
            $data = $this->renderChildren();
        }
        $GLOBALS['TSFE']->cObj->data[$field] = $data;
    }
}
