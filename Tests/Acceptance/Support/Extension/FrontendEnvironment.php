<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Tests\Acceptance\Support\Extension;

use TYPO3\CMS\Core\Cache\Backend\NullBackend;
use TYPO3\CMS\Core\Cache\Backend\SimpleFileBackend;
use TYPO3\CMS\Core\Cache\Backend\TransientMemoryBackend;
use TYPO3\CMS\Core\Cache\Backend\Typo3DatabaseBackend;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\CMS\Fluid\Core\Cache\FluidTemplateCache;
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

                'caching' => [
                    'cacheConfigurations' => [
                        'cache_hash' => [
                            'backend' => NullBackend::class,
                        ],
                        'cache_pages' => [
                            'backend' => NullBackend::class,
                        ],
                        'cache_pagesection' => [
                            'backend' => NullBackend::class,
                        ],
                        'cache_rootline' => [
                            'backend' => NullBackend::class,
                        ],
                        'cache_imagesizes' => [
                            'backend' => NullBackend::class,
                        ],
                        'assets' => [
                            'backend' => NullBackend::class,
                        ],
                        'l10n' => [
                            'backend' => NullBackend::class,
                        ],
                        'fluid_template' => [
                            'backend' => NullBackend::class,
                        ],
                        'extbase_reflection' => [
                            'backend' => NullBackend::class,
                        ],
                        'extbase_datamapfactory_datamap' => [
                            'backend' => NullBackend::class,
                        ],
                    ],
                ],
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
            'EXT:cz_simple_cal/Tests/Acceptance/Fixtures/Database/page.xml',
            'EXT:cz_simple_cal/Tests/Acceptance/Fixtures/Database/events.xml',
        ],
    ];
}
