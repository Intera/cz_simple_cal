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
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * format a localized name of a country by its isoCode
 */
class CountryNameViewHelper extends AbstractViewHelper {

	/**
	 * Get the localized name of a country from its country code
	 *
	 * @param string $isoCode the three-letter isocode of the country (as given by static_info_tables)
	 * @return string localized country name
	 * @author Christian Zenker <christian.zenker@599media.de>
	 */
	public function render($isoCode) {
		if(empty($isoCode)) {
			return '';
		}

		(!is_null(self::$staticInfoObject)) || self::init();

		if(self::$staticInfoObject === false) {
			// if init went wrong
			return $isoCode;
		}

		return self::$staticInfoObject->getStaticInfoName('COUNTRIES', $isoCode);
	}

	/**
	 * @var object
	 */
	protected static $staticInfoObject = null;

	/**
	 * init static info tables to use with this view helper
	 *
	 * @return null
	 */
	protected static function init() {
		// check if class was already initialized
		if(!is_null(self::$staticInfoObject)) {
			return;
		}

		// check if static_info_tables is installed
		if(!ExtensionManagementUtility::isLoaded('static_info_tables')) {
			self::$staticInfoObject = false;
			/** @noinspection PhpUndefinedConstantInspection */
			GeneralUtility::devLog('static_info_tables needs to be installed to use '.get_class(self), get_class(self), 1);
			return;
		}

		require_once(ExtensionManagementUtility::extPath('static_info_tables') . 'pi1/class.tx_staticinfotables_pi1.php');
		// init class
		// code taken from the documentation
		self::$staticInfoObject = &GeneralUtility::getUserObj('&tx_staticinfotables_pi1');
		if(!self::$staticInfoObject) {
			self::$staticInfoObject = false;
			return null;
		}
		if (self::$staticInfoObject->needsInit()) {
			self::$staticInfoObject->init();
		}
	}
}