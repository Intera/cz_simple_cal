<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Tests\Acceptance\Support\Extension;

use TYPO3\TestingFramework\Core\Acceptance\Extension\BackendEnvironment;

class FrontendEnvironment extends BackendEnvironment
{
    /**
     * Load a list of core extensions and styleguide
     *
     * @var array
     */
    protected $localConfig = [
        'pathsToLinkInTestInstance' => [
            'typo3conf/ext/cz_simple_cal/Tests/Acceptance/Fixtures/sites' =>
                'typo3conf/sites',
        ],
        'configurationToUseInTestInstance' => [
            'FE' => ['debug' => true],
            'SYS' => [
                'devIPmask' => '*',
                'displayErrors' => 1,
                'systemLogLevel' => 0,
                // E_WARNING | E_RECOVERABLE_ERROR | E_DEPRECATED
                'exceptionalErrors' => 12290,
            ],
        ],
        'coreExtensionsToLoad' => [
            'core',
            'extbase',
            'fluid',
            'frontend',
            'fluid_styled_content',
        ],
        'testExtensionsToLoad' => ['typo3conf/ext/cz_simple_cal'],
        'xmlDatabaseFixtures' => [
            'EXT:cz_simple_cal/Tests/Acceptance/Fixtures/page.xml',
        ],
    ];
}
