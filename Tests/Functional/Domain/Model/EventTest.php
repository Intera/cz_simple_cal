<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Tests\Functional\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Christian Zenker <christian.zenker@599media.de>, 599media GmbH
 *
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
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Countable;
use Iterator;
use IteratorAggregate;
use Tx\CzSimpleCal\Domain\Model\Category;
use Tx\CzSimpleCal\Domain\Model\Event;
use Tx\CzSimpleCal\Domain\Model\Exception;
use Tx\CzSimpleCal\Domain\Model\File;
use Tx\CzSimpleCal\Domain\Repository\EventRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * an event in an calendar
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class EventTest extends FunctionalTestCase
{
    /**
     * @var Event
     */
    protected $object = null;

    protected $testExtensionsToLoad = ['typo3conf/ext/cz_simple_cal'];

    public function setUp()
    {
        parent::setUp();

        $this->importDataSet(__DIR__ . '/../../Fixtures/basic_event_structure.xml');

        $this->setUpFrontendRootPage(2, ['EXT:cz_simple_cal/Configuration/TypoScript/main/setup.txt']);

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $eventRepository = $objectManager->get(EventRepository::class);

        $query = $eventRepository->createQuery();
        $query->getQuerySettings()
            ->setRespectStoragePage(false)
            ->setIgnoreEnableFields(true)
            ->setRespectSysLanguage(false);

        $query->setLimit(1);
        $query->matching($query->equals('title', 'eventToTestDomainModel'));

        $this->object = $query->execute()->getFirst();

        if (!$this->object) {
            self::fail('could not find the event domain object labeled "eventToTestDomainModel".');
        }
    }

    /**
     * tests if each object of an iterable structure is of a certain class or interface
     */
    public static function assertEachIsInstanceOf($array, $class, $message = null)
    {
        self::assertTrue(
            is_array($array) || $array instanceof Iterator || $array instanceof IteratorAggregate,
            sprintf(
                'the array given to check for class "%s" is an array%s',
                $class,
                $message ? ': ' . $message : null
            )
        );
        foreach ($array as $key => $object) {
            if (!$object instanceof $class) {
                self::fail(
                    $message ? $message : sprintf(
                        'Failed asserting that "%s" at key "%s" is instance of %s.',
                        is_object($object) ? get_class($object) : gettype($object),
                        $key,
                        $class
                    )
                );
            }
            self::isTrue(
                true,
                $message ? $message : sprintf(
                    'each object in array are instances of %s',
                    $class
                )
            );
        }
    }

    public function testGetCategories()
    {
        $categories = $this->object->getCategories();

        self::assertTrue(is_array($categories) || $categories instanceof Countable, 'categories can be counted');
        self::assertEquals(1, count($categories), 'exactly one category assigned');
        self::assertEachIsInstanceOf($categories, Category::class);
    }

    public function testGetCategory()
    {
        $category = $this->object->getCategory();

        self::assertTrue(
            $category instanceof Category,
            'category is a Tx_CzSimpleCal_Domain_Model_Category'
        );
    }

    public function testGetDescription()
    {
        $description = $this->object->getDescription();
        self::assertTrue(is_string($description), 'description is a string');
        self::assertFalse(empty($description), 'description is not empty');
    }

    public function testGetExceptions()
    {
        $exceptions = $this->object->getExceptions();

        self::assertTrue(is_array($exceptions) || $exceptions instanceof Countable, 'exceptions can be counted');
        // Will be covered by checking associated exceptions
        // self::assertEquals(3, count($exceptions), 'exactly three exceptions are assigned');
        self::assertEachIsInstanceOf(
            $exceptions,
            Exception::class,
            'only Tx_CzSimpleCal_Domain_Model_Exception are given (no exceptionGroups)'
        );

        $exceptionTitles = [];
        foreach ($exceptions as $exception) {
            $exceptionTitles[] = $exception->getTitle();
        }

        self::assertTrue(in_array('testException1', $exceptionTitles), 'assigned exception is present');
        self::assertTrue(in_array('testException2', $exceptionTitles), 'exception from exceptionGroup is present #1');
        self::assertTrue(in_array('testException3', $exceptionTitles), 'exception from exceptionGroup is present #2');

        self::assertFalse(
            in_array('testExceptionThatIsNotUsed', $exceptionTitles),
            'not assigned exception is not present'
        );
    }

    public function testGetFiles()
    {
        $this->setUpBackendUserFromFixture(1);

        $files = $this->object->getFiles();

        self::assertEachIsInstanceOf($files, File::class);

        /** @var File $file */
        $file = current($files);

        self::assertRegExp('/^example(_\d+)?\.doc$/', $file->getFile(), 'first download ok.');
        self::assertStringStartsWith('uploads/', $file->getFilePath(), 'filepath prepended.');
        self::assertEquals('file1', $file->getCaption(), 'caption set.');
        self::assertRegExp('/^example(_\d+)?.pdf$/', next($files)->getFile(), 'second download ok.');
    }

    public function testGetHash()
    {
        $hash = $this->object->getHash();

        self::assertTrue(is_string($hash) && strlen($hash) > 7, 'hash is a non-empty string');
    }

    public function testGetImages()
    {
        $this->setUpBackendUserFromFixture(1);

        $images = $this->object->getImages();

        self::assertEachIsInstanceOf($images, File::class);

        $image = current($images);

        self::assertRegExp('/^misc(_\d+)?\.png$/', $image->getFile(), 'first image ok.');
        self::assertStringStartsWith('uploads/', $image->getFilePath(), 'filepath prepended.');
        self::assertEquals('caption1', $image->getCaption(), 'caption set.');
        self::assertEquals('alternate1', $image->getAlternateText(), 'alternate text set.');
        self::assertRegExp('/^misc2(_\d+)?.png$/', next($images)->getFile(), 'second image ok.');
    }

    public function testGetLocation()
    {
        // TODO: initialize table-model mapping in extbase
        self::markTestIncomplete('no tests done yet');
    }

    public function testGetLocationName()
    {
        self::assertEquals('somewhere', $this->object->getLocationName());
    }

    public function testGetNextAppointment()
    {
        // @todo test
        self::markTestIncomplete('no tests done yet');
    }

    public function testGetNextAppointments()
    {
        // @todo test
        self::markTestIncomplete('no tests done yet');
    }

    public function testGetOrganizer()
    {
        // @TODO: initialize table-model mapping in extbase
        self::markTestIncomplete('no tests done yet');
    }

    public function testGetRecurrances()
    {
        // @todo test
        self::markTestIncomplete('no tests done yet');
    }

    public function testGetShowPageInstead()
    {
        self::assertEquals(123456, $this->object->getShowPageInstead());
    }

    public function testGetSlug()
    {
        self::assertStringStartsWith('eventtotestdomainmodel', $this->object->getSlug());
    }

    public function testGetTeaser()
    {
        $teaser = $this->object->getTeaser();
        self::assertTrue(is_string($teaser) && !empty($teaser), 'teaser is a non-empty string');
    }

    public function testGetTitle()
    {
        self::assertEquals('eventToTestDomainModel', $this->object->getTitle());
    }

    public function testHasEndTime()
    {
        $hasEndTime = $this->object->hasEndTime();

        self::assertTrue(is_bool($hasEndTime), 'hasEndTime returns a boolean');
    }

    public function testIsAlldayEvent()
    {
        $isAlldayEvent = $this->object->isAlldayEvent();

        self::assertTrue(is_bool($isAlldayEvent), 'isAlldayEvent returns a boolean');
    }

    public function testIsEnabled()
    {
        $isEnabled = $this->object->isEnabled();

        self::assertTrue(is_bool($isEnabled), 'isEnabled returns a boolean');
    }

    public function testIsOneDayEvent()
    {
        $isOneDayEvent = $this->object->isOneDayEvent();

        self::assertTrue(is_bool($isOneDayEvent), 'isOneDayEvent returns a boolean');
    }

    public function testIsRecurrant()
    {
        $isRecurrant = $this->object->isRecurrant();

        self::assertTrue(is_bool($isRecurrant), 'isRecurrant returns a boolean');
    }
}
