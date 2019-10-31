<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Tests\Unit\Domain\Repository;

use Tx\CzSimpleCal\Tests\Unit\Domain\Repository\Mock\EventIndexRepositoryMock;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * testing the Domain_Repository_EventIndex class
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class EventIndexRepositoryTest extends UnitTestCase
{
    /**
     * @var EventIndexRepositoryMock
     */
    protected $repository;

    /**
     * @var bool
     */
    protected $resetSingletonInstances = true;

    /**
     * @var TypoScriptService
     */
    protected $typoScriptService;

    public function setUp()
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->repository = new EventIndexRepositoryMock($objectManager);
        $this->typoScriptService = new TypoScriptService();
    }

    public function provideDataForCleanSettingsFields()
    {
        $data = [
            [
                'startDate',
                1234567890,
                1234567890,
            ],
            [
                'startDate',
                '1234567890',
                1234567890,
            ],
            [
                'startDate',
                '',
                null,
            ],
            [
                'startDate',
                '42 AND foo=bar',
                null,
            ], // XSS test

            [
                'endDate',
                1234567890,
                1234567890,
            ],
            [
                'endDate',
                '1234567890',
                1234567890,
            ],
            [
                'endDate',
                '',
                null,
            ],
            [
                'endDate',
                '42 AND foo=bar',
                null,
            ], // XSS test

            [
                'maxEvents',
                10,
                10,
            ],
            [
                'maxEvents',
                '10',
                10,
            ],
            [
                'maxEvents',
                null,
                null,
            ],
            [
                'maxEvents',
                0,
                null,
            ],
            [
                'maxEvents',
                -42,
                null,
            ],
            [
                'maxEvents',
                '42 AND foo=bar',
                null,
            ], // XSS test

            [
                'order',
                'asc',
                'asc',
            ],
            [
                'order',
                'desc',
                'desc',
            ],
            [
                'order',
                'foobar',
                null,
            ],
            [
                'order',
                ';DELETE table',
                null,
            ], // XSS test

            [
                'orderBy',
                'start',
                'start',
            ],
            [
                'orderBy',
                'end',
                'end',
            ],
            [
                'orderBy',
                'foobar',
                'foobar',
            ],
            [
                'orderBy',
                '',
                null,
            ],
            [
                'orderBy',
                'foo; DELETE table',
                null,
            ],
            [
                'groupBy',
                'day',
                'day',
            ],
            [
                'groupBy',
                'foobar',
                'foobar',
            ],
            [
                'groupBy',
                '',
                null,
            ],
            [
                'groupBy',
                'foo; DELETE table',
                null,
            ], // XSS test

            [
                'includeStartedEvents',
                true,
                true,
            ],
            [
                'includeStartedEvents',
                '1',
                true,
            ],
            [
                'includeStartedEvents',
                1,
                true,
            ],
            [
                'includeStartedEvents',
                false,
                false,
            ],
            [
                'includeStartedEvents',
                '0',
                false,
            ],
            [
                'includeStartedEvents',
                0,
                false,
            ],
            [
                'includeStartedEvents',
                'KILL ALL HUMANS',
                false,
            ],
            [
                'includeStartedEvents',
                null,
                false,
            ],

            [
                'excludeOverlongEvents',
                true,
                true,
            ],
            [
                'excludeOverlongEvents',
                '1',
                true,
            ],
            [
                'excludeOverlongEvents',
                1,
                true,
            ],
            [
                'excludeOverlongEvents',
                false,
                false,
            ],
            [
                'excludeOverlongEvents',
                '0',
                false,
            ],
            [
                'excludeOverlongEvents',
                0,
                false,
            ],
            [
                'excludeOverlongEvents',
                'KILL ALL HUMANS',
                false,
            ],
            [
                'excludeOverlongEvents',
                null,
                false,
            ],
        ];

        $label = $data;
        array_walk($label, [$this, 'nameDataProviderForCleanSettingsFields']);

        return array_combine($label, $data);
    }

    /**
     * @dataProvider provideDataForCleanSettingsFields
     *
     * @param $field
     * @param $value
     * @param $assumed
     */
    public function testCleanSettingsFields($field, $value, $assumed)
    {
        $ret = $this->repository->cleanSettings([$field => $value]);

        $this->assertSame($assumed, $ret[$field]);
    }

    /**
     * just a very simple basic test
     */
    public function testCleanSettingsFieldsForFiltersBasic()
    {
        $ret = $this->repository->cleanSettings(['filter' => ['foo' => 'bar']]);

        $this->assertSame(
            [
                'foo' => ['value' => ['bar']],
            ],
            $ret['filter']
        );
    }

    public function testCleanSettingsFieldsForFiltersCheckAdditionalInstructions()
    {
        $ret = $this->repository->cleanSettings(
            [
                'filter' => [
                    'foo' => [
                        'value' => 'bar',
                        'negate' => '1',
                    ],
                ],
            ]
        );

        $this->assertSame(
            [
                'foo' => [
                    'value' => ['bar'],
                    'negate' => '1',
                ],
            ],
            $ret['filter']
        );
    }

    /**
     * @see http://forge.typo3.org/issues/14093
     */
    public function testCleanSettingsFieldsForFiltersCheckFlexformOverridesDefault()
    {
        $config = $this->typoScriptService->convertTypoScriptArrayToPlainArray(
            [
                'foo' => '1,2,3',
                'foo.' => [
                    'value' => '42',
                    'negate' => '1',
                ],
            ]
        );

        $ret = $this->repository->cleanSettings(['filter' => $config]);

        $this->assertSame(
            [
                1,
                2,
                3,
            ],
            $ret['filter']['foo']['value'],
            'values where correctly overriden'
        );
        $this->assertArrayNotHasKey('_typoScriptNodeValue', $ret['filter']['foo'], '_typoScriptNodeValue was unset');
    }

    /**
     * test that multiple given values are converted into an array
     */
    public function testCleanSettingsFieldsForFiltersCheckMultipleValuesAreConvertedToAnArray()
    {
        $ret = $this->repository->cleanSettings(['filter' => ['foo' => 'bar,baz,42']]);

        $this->assertSame(
            [
                'foo' => [
                    'value' => [
                        'bar',
                        'baz',
                        42,
                    ],
                ],
            ],
            $ret['filter']
        );
    }

    /**
     * test that selecting the fields to filter recursive over multiple tables
     */
    public function testCleanSettingsFieldsForFiltersCheckNestingOfFields()
    {
        $ret = $this->repository->cleanSettings(
            [
                'filter' => [
                    'foo' => [
                        'baz' => 'bar',
                        'bar' => [
                            'superfoo' => 'someothervalue',
                            'foo' => ['value' => 'foobar'],
                        ],
                    ],
                ],
            ]
        );
        $this->assertArrayHasKey('foo.baz', $ret['filter'], 'one level down');
        $this->assertArrayHasKey('foo.bar.superfoo', $ret['filter'], 'two levels down');
        $this->assertArrayNotHasKey('foo.bar.foo.value', $ret['filter'], 'keywords are not converted');
    }

    /**
     * test that numbers given are converted to integers
     */
    public function testCleanSettingsFieldsForFiltersCheckNumbersAreConvertedToIntegers()
    {
        $ret = $this->repository->cleanSettings(['filter' => ['foo' => '42']]);

        $this->assertSame(
            [
                'foo' => ['value' => [42]],
            ],
            $ret['filter']
        );
    }

    public function testCleanSettingsFieldsForFiltersCheckValuesFromTyposcript()
    {
        $config = $this->typoScriptService->convertTypoScriptArrayToPlainArray(
            [
                'foo' => 'bar',
                'foo.' => ['negate' => '1'],
            ]
        );

        $ret = $this->repository->cleanSettings(['filter' => $config]);

        $this->assertSame(
            [
                'foo' => [
                    'negate' => '1',
                    'value' => ['bar'],
                ],
            ],
            $ret['filter']
        );
    }

    protected function nameDataProviderForCleanSettingsFields(
        &$value,
        /** @noinspection PhpUnusedParameterInspection */
        $key
    ) {
        $value = sprintf(
            '%s set to %s:%s returns %s',
            $value[0],
            gettype($value[1]),
            htmlspecialchars(print_r($value[1], true)),
            htmlspecialchars(print_r($value[2], true))
        );
    }
}
