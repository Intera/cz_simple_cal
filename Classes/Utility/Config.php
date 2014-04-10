<?php
namespace Tx\CzSimpleCal\Utility;

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
 * A class holding configuration from the extensions configuration in TYPO3_CONF
 */
class Config {

	/**
	 * the configuration
	 * @var array
	 */
	protected static $data = null;

	/**
	 * get a value
	 * @param string $name
	 * @throws \InvalidArgumentException
	 */
	public static function get($name) {
		self::init();

		if(!self::exists($name)) {
			throw new \InvalidArgumentException(sprintf('The value "%s" was not set. Did you update the Extensions settings?', $name));
		}

		return self::$data[$name];
	}

	/**
	 * check if the value exists
	 *
	 * @param $name
	 * @return boolean
	 */
	public static function exists($name) {
		self::init();
		return is_array(self::$data) && array_key_exists($name, self::$data);
	}

	/**
	 * set a value of an array of values
	 *
	 * @param string|array $name
	 * @param string $value
	 * @throws \InvalidArgumentException
	 */
	public static function set($name, $value = null) {
		self::init();
		if(is_string($name)) {
			self::$data[$name] = $value;
		} elseif(is_array($name)) {
			self::$data = array_merge(
				self::$data,
				$name
			);
		} else {
			throw new \InvalidArgumentException('The value "name" must be a string or array.');
		}
	}

	/**
	 * initializing method that will be called as soon as it is needed
	 */
	protected static function init() {
		if(is_null(self::$data)) {
			self::$data = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['cz_simple_cal']);
		}
	}
}