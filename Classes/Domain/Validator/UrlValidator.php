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
 * sanitizes and validates a given url
 */
class UrlValidator extends AbstractValidator
{
    public function isValid($value)
    {
        $setterMethodName = 'set' . $this->options['propertyName'];
        $getterMethodName = 'get' . $this->options['propertyName'];
        $object = $this->options['object'];

        // Check that value and domain property match
        if ($value != $object->{$getterMethodName}()) {
            throw new \RuntimeException(
                'the given value and the value of the object don\'t match in ' . get_class($this)
            );
        }

        if (empty($value)) {
            return true;
        }

        if (strpos($value, '://') === false) {
            $value = 'http://' . $value;
        }

        $value = filter_var($value, FILTER_VALIDATE_URL);

        if ($value === false) {
            $this->addError('The url does not seem valid.', 'invalid');
            return false;
        }

        $object->{$setterMethodName}($value);
        return true;
    }
}
