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
        $this->openPageAlias($I, 'action-day');

        $I->canSeeElement(['css' => 'div.vcalendar-day']);
        $I->canSee('Events on January 1, 2010', 'h2');
        $I->canSeeNumberOfElements('//*[contains(@class, "vevent")]', 1);
    }

    protected function openPageAlias(FrontendTester $I, string $alias): void
    {
        $I->amOnPage('/');
        $I->click($alias);
    }
}
