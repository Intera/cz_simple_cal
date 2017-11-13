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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * sanitizes and validates a list of tweets
 */
class TwitterHashtagValidator extends AbstractValidator
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

        // Required
        if (empty($value)) {
            if ($this->options['required']) {
                $this->addError('no value given', 'required');
                return false;
            } else {
                return true;
            }
        }

        $tags = GeneralUtility::trimExplode(',', $value);
        if ($this->options['minimum'] && $this->options['minimum'] > count($tags)) {
            $this->addError(sprintf('at least %d items required', $this->options['minimum']), 'minimum');
            return false;
        }

        if ($this->options['maximum'] && $this->options['maximum'] > count($tags)) {
            $this->addError(sprintf('at max %d items allowed', $this->options['maximum']), 'maximum');
            return false;
        }

        foreach ($tags as &$tag) {
            if (!preg_match('/^#?[\pL\pN\-]{2,40}$/i', $tag)) {
                $this->addError(sprintf('"%s" is not a valid hashtag.', $tag), 'invalid');
                return false;
            }
            $tag = '#' . ltrim($tag, '#');
        }

        $value = implode(', ', $tags);

        $object->{$setterMethodName}($value);
        return true;
    }
}
