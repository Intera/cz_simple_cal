<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Tests\Acceptance\Frontend;

use Tx\CzSimpleCal\Tests\Acceptance\Support\FrontendTester;

/**
 * Sampletest that do some more tests
 */
class CalendarPluginCest
{
    public function actionDay(FrontendTester $I)
    {
        $this->openPageAlias($I, 'action-day', '&tx_czsimplecal_pi1[getDate]=2010-01-01');

        $I->canSeeElement(['css' => 'div.vcalendar-day']);
        $I->canSeeElement('//h2[. = "Events on January  1, 2010"]');
        $I->canSeeNumberOfElements('//*[contains(@class, "vevent")]', 1);
    }

    protected function openPageAlias(FrontendTester $I, string $alias, ?string $parameters = null): void
    {
        $url = '/' . $alias;

        if ($parameters) {
            $parameters = ltrim($parameters, '&');
            $url .= '?' . $parameters;
        }

        $I->amOnPage($url);
    }
}
