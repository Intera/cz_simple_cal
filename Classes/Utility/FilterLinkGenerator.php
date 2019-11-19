<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Utility;

use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

class FilterLinkGenerator
{
    /**
     * @var array
     */
    private $arguments = [];

    /**
     * @var ControllerContext
     */
    private $controllerContext;

    /**
     * @var array
     */
    private $excludedArguments = [];

    public function generateLink(
        string $filterArgument,
        ?string $filterValue,
        string $action,
        TagBuilder $tagBuilder,
        ControllerContext $controllerContext
    ): string {
        $this->controllerContext = $controllerContext;
        $this->arguments = [];

        // This is needed because otherwise it can happen that an empty
        // string is assigned to this variable and an invalid cHash is
        // generated. To prevent this we exclude the variable.
        // This only affects the string parameter. The filter array is
        // still available in the URL.
        $this->excludedArguments = ['tx_czsimplecal_pi1[filter]'];

        $this->initArguments($filterArgument, $filterValue);

        $uri = $this->controllerContext
            ->getUriBuilder()
            ->reset()
            ->setArguments($this->arguments)
            ->setAddQueryString(true)
            ->setArgumentsToBeExcludedFromQueryString($this->excludedArguments)
            ->uriFor($action);

        $tagBuilder->setTagName('a');
        $tagBuilder->addAttribute('href', $uri);
        $tagBuilder->addAttribute('rel', 'nofollow');
        $tagBuilder->forceClosingTag(true);

        return $tagBuilder->render();
    }

    /**
     * @param string $filterArgument
     * @param string|null $filterValue
     */
    protected function initArguments(string $filterArgument, ?string $filterValue): void
    {
        $newFilter = [];
        if (isset($filterValue)) {
            $newFilter[$filterArgument] = $filterValue;
        }

        $currentArguments = $this->controllerContext->getRequest()->getArguments();
        $existingFilters = $currentArguments['filter'] ?? [];
        $existingFilters = array_diff_key($existingFilters, [$filterArgument => 'dummy']);

        $newFilters = array_merge(
            $existingFilters,
            $newFilter
        );

        if ($newFilters === []) {
            $this->excludedArguments[] = 'tx_czsimplecal_pi1[filter][' . $filterArgument . ']';
            return;
        }

        $this->arguments = ['tx_czsimplecal_pi1' => ['filter' => $newFilters]];
    }
}
