<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\ViewHelpers;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Alexander Stehlik <astehlik.deleteme@intera.de>, Intera GmbH
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

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * This view helper loops over all configurations defined within settings.renderData and builds
 * an associative array where the configuration keys are used as array keys.
 *
 * The child array will initially be filled with the data of the current content object.
 *
 * There configuration options can be used to fill the child arrays with values:
 *
 * mapObjectProperties: This configuration will use the configuration key as array key
 * and calls a get method on the current object with the configuration key value.
 *
 * overrideData: This configuration can be used to fill the array with plain values.
 *
 * An example:
 *
 * The settings are:
 *
 * settings.renderData.files {
 *    mapObjectProperties {
 *        uid = uid
 *    }
 *    overrideData {
 *        header =
 *    }
 * }
 *
 * The curernt content object contains this data array:
 *
 * array(
 *    'pid' => 5,
 *    'header' => 'my plugin'
 * )
 *
 * This will result in this result array:
 *
 * array(
 *    'files' => array(
 *        'pid' => 5,
 *        'uid' => $object->getUid(),
 *        'header' => ''
 *    )
 * )
 *
 * The render data can now be used in CObjectViewHelper calls for the data attribute:
 *
 * <f:cObject typoscriptObjectPath="plugin.tx_czsimplecal.settings.renderData.files.renderObject"
 * data="{renderData.files}" />
 */
class RenderDataViewHelper extends AbstractViewHelper
{
    /**
     *
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @var array
     */
    protected $contentObjectData;

    /**
     * @var array
     */
    protected $settings;

    /**
     * Initialize all arguments. You need to override this method and call
     * $this->registerArgument(...) inside this method, to register all your arguments.
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument(
            'object',
            'object',
            'The configured mapObjectProperties will be read from this object using getter methods.',
            true
        );
        $this->registerArgument(
            'renderDataVariable',
            'string',
            'The name of the variable that will be available during child object rendering.',
            false,
            'renderData'
        );
        $this->registerArgument('extensionName', 'string', 'The extension name that is used to fetch the settings.');
        $this->registerArgument('pluginName', 'string', 'The plugin name that is used to fetch the settings.');
    }

    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * Builds the render data array.
     *
     * @return string
     */
    public function render(): string
    {
        $object = $this->arguments['object'];
        $renderDataVariable = $this->arguments['renderDataVariable'];

        $this->initializeClassVariables();
        $renderData = [];

        foreach ($this->settings['renderData'] as $variableName => $dataSettings) {
            $data = $this->contentObjectData;

            if (is_array($dataSettings['mapObjectProperties'])) {
                foreach ($dataSettings['mapObjectProperties'] as $dataProperty => $eventProperty) {
                    $propertyGetter = 'get' . ucfirst($eventProperty);
                    $data[$dataProperty] = $object->$propertyGetter();
                }
            }

            if (is_array($dataSettings['overrideData'])) {
                $data = array_merge($data, $dataSettings['overrideData']);
            }

            $renderData[$variableName] = $data;
        }

        $this->templateVariableContainer->add($renderDataVariable, $renderData);
        $result = $this->renderChildren();
        $this->templateVariableContainer->remove($renderDataVariable);

        return $result;
    }

    /**
     * Initializes the settings and the contentObjectData class variables required for rendering.
     *
     * @return void
     */
    protected function initializeClassVariables()
    {
        $this->contentObjectData = $this->configurationManager->getContentObject()->data;
        $extensionName = $this->hasArgument('extensionName') ? $this->arguments['extensionName'] : null;
        $pluginName = $this->hasArgument('pluginName') ? $this->arguments['pluginName'] : null;
        $this->settings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            $extensionName,
            $pluginName
        );
    }
}
