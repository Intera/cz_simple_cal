<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Christian Zenker <christian.zenker@599media.de>, 599media GmbH
 *
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
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * This utility class generates the available actions for the
 * switchableControllerActions setting in the FlexForm.
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class Tx_CzSimpleCal_Utility_FlexConfig {

	/**
	 * Initializes the available action configurations in the given
	 * $config['items'] array.
	 *
	 * @param array $config The current configuration of the FlexForm select field.
	 * @return void
	 */
	public function getAllowedActions($config) {

		$pid = $config['row']['pid'];
		$tsConfig = \TYPO3\CMS\Backend\Utility\BackendUtility::getPagesTSconfig($pid);

		$flexConfig = & $tsConfig['options.']['cz_simple_cal_pi1.']['flexform.'];
		if (empty($flexConfig) || !is_array($flexConfig['allowedActions.'])) {
			return;
		}

		$availableActions = $flexConfig['allowedActions.']['availableActions.'];
		if (!is_array($availableActions)) {
			return;
		}

		if (isset($flexConfig['allowedActions.']['enabledActions'])) {
			$enabled = array();
			foreach (\TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $flexConfig['allowedActions.']['enabledActions'], TRUE) as $i) {
				$enabled[$i . '.'] = '';
			}
			$allowedActions = array_intersect_key($availableActions, $enabled);
		} else {
			$allowedActions = $availableActions;
		}

		foreach ($allowedActions as $name => $actionConfiguration) {

			$value = $actionConfiguration['value'];
			$label = $this->getLanguageService()->sL($actionConfiguration['label']);

			if (empty($label)) {
				$label = $value;
			}

			$config['items'][$name] = array(
				$label,
				$value
			);
		}
	}

	/**
	 * Getter for the language service.
	 *
	 * Introduced for easier Unit testing and IDE support.
	 *
	 * @return \TYPO3\CMS\Lang\LanguageService
	 */
	protected function getLanguageService() {
		return $GLOBALS['LANG'];
	}
}