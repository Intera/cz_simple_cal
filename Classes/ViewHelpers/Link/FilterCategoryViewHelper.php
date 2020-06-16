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
use Tx\CzSimpleCal\Utility\ExtensionConfiguration;
use Tx\CzSimpleCal\Utility\FilterLinkGenerator;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Link category view helper.
 */
class FilterCategoryViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var ExtensionConfiguration
     */
    protected $extensionConfiguration;

    /**
     * Registers the universal tag attributes like class, id etc.
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('category', Category::class, '', false, null);
        $this->registerArgument('action', 'string', '', false, 'listMonths');
        $this->registerUniversalTagAttributes();
    }

    public function injectExtensionConfiguration(ExtensionConfiguration $extensionConfiguration)
    {
        $this->extensionConfiguration = $extensionConfiguration;
    }

    /**
     * Renders a link that will only list events of the given category.
     *
     * @return string Rendered link
     */
    public function render()
    {
        $this->tag->setContent($this->renderChildren());

        $category = $this->getCategory();

        $filterValue = null;
        if (isset($category)) {
            $filterValue = (string)$category->getUid();
        }

        $filterLinkGenerator = new FilterLinkGenerator();
        return $filterLinkGenerator->generateLink(
            $this->getCategoryFilterProperty(),
            $filterValue,
            $this->arguments['action'],
            $this->tag,
            $this->getControllerContext()
        );
    }

    private function getCategory(): ?Category
    {
        return $this->arguments['category'];
    }

    private function getCategoryFilterProperty(): string
    {
        return $this->extensionConfiguration->isUsingSingleCategory() ? 'category.uid' : 'categories.uid';
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
