<?php
namespace Tx\CzSimpleCal\ViewHelpers\Format;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "cz_simple_cal".            *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Trim view helper.
 */
class TrimViewHelper extends AbstractViewHelper {

	/**
	 * Removes whitespace around the rendered children.
	 *
	 * @return string
	 */
	public function render() {
		$data = $this->renderChildren();
		return trim($data);
	}
}
