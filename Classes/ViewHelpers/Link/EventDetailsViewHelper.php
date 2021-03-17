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

use Tx\CzSimpleCal\Domain\Model\EventIndex;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Extbase\Service\ExtensionService;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Link to event details.
 *
 * IMPORTANT! This view helper should only be required when linking between different plugins and the
 * view.defaultPid from the FelxForm settings ist used.
 */
class EventDetailsViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var ExtensionService
     */
    protected $extensionService;

    /**
     * Registers the universal tag attributes like class, id etc.
     *
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('event', EventIndex::class, '', true);
        $this->registerUniversalTagAttributes();
    }

    public function injectExtensionService(ExtensionService $extensionService)
    {
        $this->extensionService = $extensionService;
    }

    /**
     * Renders a link that will only list events of the given category.
     *
     * @return string Rendered link
     */
    public function render()
    {
        $event = $this->getEvent();

        $uriBuilder = $this->getControllerContext()->getUriBuilder();
        $uriBuilder->reset();

        // This is the central part of this view helper! We overwrite the argument prefix to make the
        // parameters available to the target plugin. We can not provide the target plugin to uriFor
        // because then the view.defaultPid setting from the FelxForm is ignored!
        $targetPluginNamespace = $this->extensionService->getPluginNamespace(
            $this->getControllerContext()->getRequest()->getControllerExtensionName(),
            'Pi1'
        );
        $uriBuilder->setArgumentPrefix($targetPluginNamespace);

        $uri = $uriBuilder->uriFor(
            'show',
            ['event' => $event],
            'EventIndex',
            $this->getControllerContext()->getRequest()->getControllerExtensionName(),
            $this->getControllerContext()->getRequest()->getPluginName()
        );

        $this->tag->setTagName('a');
        $this->tag->addAttribute('href', $uri);
        $this->tag->setContent($this->renderChildren());
        $this->tag->forceClosingTag(true);

        return $this->tag->render();
    }

    private function getControllerContext(): ControllerContext
    {
        return $this->getRenderingContext()->getControllerContext();
    }

    private function getEvent(): EventIndex
    {
        return $this->arguments['event'];
    }

    private function getRenderingContext(): RenderingContext
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->renderingContext;
    }
}
