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
 * A base class for all domain models of cz_simple_cal
 */
abstract class Base extends AbstractEntity {

	/**
	 * __call
	 * TODO: check if this is still needed in the current version.
	 *
	 * @param string $methodName
	 * @param array $arguments
	 * @return mixed
	 */
	public function __call($methodName, $arguments) {
		/* this makes methods starting with "is..." and "has..." available to fluid templates
		 * without the need of writing custom handlers.
		 *
		 * By default Extbase *only* calls methods starting with "get...", but
		 * "getIsFoobar" is a rather stupid method name.
		 *
		 * Usage:
		 * 	{object.isFoobar}
		 * will call isFoobar() on object.
		 */

		if(strncmp('getIs', $methodName, 5) === 0) {
			// if: the called method starts with "getIs..."
			$methodName = 'is'.substr($methodName, 5);
			//@todo: check if method is public -> this might be done by a Reflection class, but it should use Extbase internals
			if(method_exists($this, $methodName)) {
				return call_user_func_array(array($this, $methodName), $arguments);
			}
		}

		if(strncmp('getHas', $methodName, 6) === 0) {
			// if: the called method starts with "getHas..."
			$methodName = 'has'.substr($methodName, 6);
			//@todo: check if method is public -> this might be done by a Reflection class, but it should use Extbase internals
			if(method_exists($this, $methodName)) {
				return call_user_func_array(array($this, $methodName), $arguments);
			}
		}

		return NULL;
	}
}