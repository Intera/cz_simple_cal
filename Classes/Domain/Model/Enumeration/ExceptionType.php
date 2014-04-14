<?php
namespace Tx\CzSimpleCal\Domain\Model\Enumeration;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Alexander Stehlik <astehlik.deleteme@intera.de>, Intera GmbH
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

use TYPO3\CMS\Core\Type\Enumeration;

/**
 * The type of an event exception.
 */
class ExceptionType extends Enumeration {

	/**
	 * This exception type will remove the event from the index for the configured date(s).
	 *
	 * @const
	 */
	const HIDE_EVENT = 'HideEvent';

	/**
	 * This exception type will overwrite some event properties for the configured date(s).
	 */
	const UPDATE_EVENT = 'UpdateEvent';

	/**
	 * Default is "HideEvent"
	 *
	 * @const
	 */
	const __default = 'HideEvent';
}