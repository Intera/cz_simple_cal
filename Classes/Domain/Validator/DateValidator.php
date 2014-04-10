<?php
namespace Tx\CzSimpleCal\Domain\Validator;

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

use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * sanitizes and validates a given date
 */
class DateValidator extends AbstractValidator {

	public function isValid($value) {
		$setterMethodName = 'set'.$this->options['propertyName'];
		$getterMethodName = 'get'.$this->options['propertyName'];
		$object = $this->options['object'];

		// check that value and domain property match
		if($value != $object->{$getterMethodName}()) {
			throw new \RuntimeException('the given value and the value of the object don\'t match in '.get_class($this));
		}

		// required
		if(empty($value)) {
			if($this->options['required']) {
				$this->addError('no value given', 'required');
				return false;
			} else {
				return true;
			}
		}

		// sanitize input
		if(is_numeric($value) && $value > 0) {
			$object->{$setterMethodName}(intval($value));
			$value = intval($value);
		} else {
			$day = \Tx\CzSimpleCal\Utility\StrToTime::strtotime($value.' | 00:00');
			if($day === false) {
				$this->addError('could not be parsed.', 'parseError');
				return false;
			} else {
				$object->{$setterMethodName}($day);
				$value = $day;
			}
		}

		// minimum
		if($this->options['minimum']) {
			if($value < $this->options['minimum']) {
				$this->addError(sprintf('No dates before %s allowed.', strftime('%Y-%m-%d', $this->options['minimum'])), 'minimum');
				return false;
			}
		}

		// maximum
		if($this->options['maximum']) {
			if($value > $this->options['maximum']) {
				$this->addError(sprintf('No dates after %s allowed.', strftime('%Y-%m-%d', $this->options['maximum'])), 'maximum');
				return false;
			}
		}

		return true;

	}
}