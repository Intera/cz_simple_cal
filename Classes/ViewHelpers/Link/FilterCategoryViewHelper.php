<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\ViewHelpers\Link;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "cz_simple_cal_mods".       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Tx\CzSimpleCal\Domain\Model\Category;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Link category view helper.
 */
class FilterCategoryViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * Registers the universal tag attributes like class, id etc.
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('category', Category::class, '', false, null);
        $this->registerArgument('action', 'string', '', false, 'monthList');
        $this->registerUniversalTagAttributes();
    }

    /**
     * Renders a link that will only list events of the given category.
     *
     * @return string Rendered link
     */
    public function render()
    {
        $category = $this->getCategory();
        $arguments = [];

        // This is needed because otherwise it can happen that an empty
        // string is assigned to this variable and an invalid cHash is
        // generated. To prevent this we exclude the variable.
        // This only affects the string parameter. The filter array is
        // still available in the URL.
        $excludedArguments = ['tx_czsimplecal_pi1[filter]'];

        if (isset($category)) {
            $arguments = ['tx_czsimplecal_pi1[filter][categories.uid]' => $category->getUid()];
        } else {
            $excludedArguments[] = 'tx_czsimplecal_pi1[filter][categories.uid]';
        }

        $uriBuilder = $this->getControllerContext()->getUriBuilder();
        $uri = $uriBuilder
            ->reset()
            ->setArguments($arguments)
            ->setAddQueryString(true)
            ->setArgumentsToBeExcludedFromQueryString($excludedArguments)
            ->uriFor($this->arguments['action']);

        $this->tag->setTagName('a');
        $this->tag->addAttribute('href', $uri);
        $this->tag->setContent($this->renderChildren());
        $this->tag->forceClosingTag(true);

        return $this->tag->render();
    }

    private function getCategory(): ?Category
    {
        return $this->arguments['category'];
    }

    private function getControllerContext(): ControllerContext
    {
        return $this->getRenderingContext()->getControllerContext();
    }

    private function getRenderingContext(): RenderingContext
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->renderingContext;
    }
}
