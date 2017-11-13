<?php

namespace Tx\CzSimpleCal\Domain\Model;

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

/**
 * A category of an event
 */
class Category extends Base
{
    /**
     * if respected by the template a TYPO3 page is linked
     *
     * as there is no Category-Controller yet, thats the only way to link a page according to
     * a controller. So you could create a page with information on your category for example.
     *
     * @var string showPageInstead
     */
    protected $showPageInstead;

    /**
     * a title for this category
     *
     * @var string
     */
    protected $title;

    /**
     * getter for showPageInstead
     *
     * @return string
     */
    public function getShowPageInstead()
    {
        return $this->showPageInstead;
    }

    /**
     * Getter for title
     *
     * @return string a title for this category
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * setter for showPageInstead
     *
     * @param string $showPageInstead
     * @return Category
     */
    public function setShowPageInstead($showPageInstead)
    {
        $this->showPageInstead = $showPageInstead;
        return $this;
    }

    /**
     * Setter for title
     *
     * @param string $title a title for this category
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
}
