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

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * A base class for addresses that extends tt_address
 */
class Address extends AbstractEntity
{
    /**
     * the property address
     *
     * @var string address
     */
    protected $address;

    /**
     * the property city
     *
     * @var string city
     */
    protected $city;

    /**
     * the property country
     *
     * @var string country
     */
    protected $country;

    /**
     * the property hidden
     *
     * @var boolean hidden
     */
    protected $hidden;

    /**
     * The homepage for this address (typolink parameter).
     *
     * @var string
     */
    protected $homepage;

    /**
     * the property name
     *
     * @var string name
     */
    protected $name = '';

    /**
     * the property pid
     *
     * @var integer pid
     */
    protected $pid;

    /**
     * the property zip
     *
     * @var string zip
     */
    protected $zip;

    /**
     * getter for address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * getter for city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * getter for country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * getter for hidden
     *
     * @return boolean
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * @return string
     */
    public function getHomepage()
    {
        return $this->homepage;
    }

    /**
     * getter for name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * getter for zip
     *
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * setter for hidden
     *
     * @param boolean $hidden
     * @return Address
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
        return $this;
    }

    /**
     * @param string $homepage
     */
    public function setHomepage($homepage)
    {
        $this->homepage = $homepage;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param int $pid
     */
    public function setPid($pid)
    {
        $this->pid = $pid;
    }

    /**
     * @param string $zip
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }
}
