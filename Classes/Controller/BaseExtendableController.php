<?php

namespace Tx\CzSimpleCal\Controller;

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

use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchActionException;

/**
 * Base controller for all cz_simple_cal controllers. Initializes
 * the settings array and the submitted GET / POST parameters.
 */
abstract class BaseExtendableController extends ActionController
{
    /**
     * an array of settings of the specific action used
     *
     * @var array
     */
    protected $actionSettings = null;

    /**
     * Initializes the current action
     *
     * @return void
     */
    protected function initializeAction()
    {
        $this->initializeSettings();
        $this->initializeActionSettings();
    }

    /**
     * set up all settings correctly allowing overrides from flexforms
     *
     * @return void
     */
    protected function initializeActionSettings()
    {
        // Fetch default settings.
        $actionSettings = (array)$this->settings['defaultActionSettings'];

        // Merge the current action settings.
        ArrayUtility::mergeRecursiveWithOverrule(
            $actionSettings,
            (array)$this->settings[$this->request->getControllerName(
            )]['actions'][$this->request->getControllerActionName()]
        );

        // Merge the settings from the flexform
        if (isset($this->settings['override']['action'])) {
            // This will override values if they are not empty
            ArrayUtility::mergeRecursiveWithOverrule(
                $actionSettings,
                $this->settings['override']['action'],
                false,
                false
            );
        }

        // Merge settings from getPost-values
        if (isset($actionSettings['getPostAllowed'])) {
            $allowed = GeneralUtility::trimExplode(',', $actionSettings['getPostAllowed'], true);

            $actionSettings = array_merge(
                $actionSettings,
                array_intersect_key(
                    $this->request->getArguments(),
                    array_flip($allowed)
                )
            );
        }

        $this->actionSettings = &$actionSettings;
        $this->settings[$this->request->getControllerName()]['actions'][$this->request->getControllerActionName(
        )] = &$actionSettings;
    }

    /**
     * Merges the override settings defined in the FlexForm to the current
     * settings.
     *
     * @return void
     */
    protected function initializeSettings()
    {
        if (isset($this->settings['override'])) {
            // This will override values if they are not empty and they already exist (so no adding of keys)
            ArrayUtility::mergeRecursiveWithOverrule($this->settings, $this->settings['override'], true, false);
        }
    }

    /**
     * Tries to resolve the active controller action metheod.
     *
     * If the current method does not exists and a fallback method is configured for
     * the current action this method will be used instead, e.g. this configuration
     * will use the list method for displaying the week action.
     *
     * settings.EventIndex.actions.week.useAction = list
     *
     * @return string
     * @throws NoSuchActionException
     */
    protected function resolveActionMethodName()
    {
        try {
            $methodName = parent::resolveActionMethodName();
        } catch (NoSuchActionException $e) {
            $controllerName = $this->request->getControllerName();
            $actionName = $this->request->getControllerActionName();
            if (!empty($this->settings[$controllerName]['actions'][$actionName]['useAction'])) {
                $methodName = $this->settings[$controllerName]['actions'][$actionName]['useAction'] . 'Action';
            } else {
                throw $e;
            }
        }
        return $methodName;
    }
}
