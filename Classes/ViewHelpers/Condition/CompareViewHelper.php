<?php

namespace Tx\CzSimpleCal\ViewHelpers\Condition;

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
 * A view helper to do a mathematical comparison on two values.
 *
 * This view helper is best used in conjunction with the ifViewHelper.
 *
 * These are the operators that might be used:
 *
 *    * "=" or "=="    check if both values are equal (integer 10 would be equal to string "10")
 *  * "==="          check if both values are identical (integer 10 would NOT be equal to string "10")
 *  * "!=" or "<>"   check if both values are not equal
 *  * "!=="          do an additional type check
 *  * ">"            check if the first value is larger than the second one
 *  * ">=" or "=>"   check if the first value is larger or equal than the second one
 *  * "<"            check if the first value is smaller than the second one
 *  * "<=" or "=<"   check if the first value is smaller or equal than the second one
 *
 * <code title="basic example">
 *      <f:if condition="{x:condition.compare(value1: 10, value2: 10)}">Both values are equal</f:if>
 * </code>
 *
 * Condition would evaluate to true as "=" is the default comparison.
 *
 *
 * <code title="integer vs string 1">
 *      <f:if condition="{x:condition.compare(value1: 10, value2: '10', operation:'=')}">Both values are equal</f:if>
 * </code>
 *
 * Condition would evaluate to true as "=" does not do a type check.
 *
 *
 * <code title="integer vs string 2">
 *     <f:if condition="{x:condition.compare(value1: 10, value2: '10', operation:'===')}">Both values are equal</f:if>
 * </code>
 *
 * Condition would evaluate to false as "===" does a type check.
 *
 *
 * <code title="comparing strings">
 *     <f:if condition="{x:condition.compare(value1: 'foo', value2: 'foo')}">Both values are equal</f:if>
 * </code>
 *
 * Condition would evaluate to true as both strings are equal.
 *
 *
 * <code title="comparing object method results">
 *     <f:if condition="{x:condition.compare(value1: person.age, value2: 18, operation='&lt;')}">You are too
 * young</f:if>
 * </code>
 */
class CompareViewHelper extends AbstractViewHelper
{
    /**
     * Compare two values
     *
     * @param mixed $value1 first value
     * @param mixed $value2 second value
     * @param string $operation a string for the operation
     * @return boolean if the condition is met
     * @throws \InvalidArgumentException
     */
    public function render($value1, $value2, $operation = '=')
    {
        $operation = htmlspecialchars_decode($operation);

        if ($operation === '=' || $operation === '==') {
            return $value1 == $value2;
        } elseif ($operation === '===') {
            return $value1 === $value2;
        } elseif ($operation === '!=' || $operation === '<>') {
            return $value1 != $value2;
        } elseif ($operation === '!==') {
            return $value1 !== $value2;
        } elseif ($operation === '>') {
            return $value1 > $value2;
        } elseif ($operation === '>=' || $operation === '=>') {
            return $value1 >= $value2;
        } elseif ($operation === '<') {
            return $value1 < $value2;
        } elseif ($operation === '<=' || $operation === '=<') {
            return $value1 <= $value2;
        } else {
            throw new \InvalidArgumentException(
                sprintf('The operation "%s" is unknown. Please see the documentation for valid values.', $operation)
            );
        }
    }
}
