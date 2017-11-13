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
 * get the hostname of a url
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class HostNameViewHelper extends AbstractViewHelper
{
    /**
     * Get the localized name of a country from its country code
     *
     * @param string $uri
     * @return string hostname of the uri
     * @author Christian Zenker <christian.zenker@599media.de>
     */
    public function render($uri)
    {
        if (empty($uri)) {
            return '';
        }

        if (strpos($uri, '://') === false) {
            $uri = 'http://' . $uri;
        }
        $parts = parse_url($uri);
        return $parts['host'];
    }
}
