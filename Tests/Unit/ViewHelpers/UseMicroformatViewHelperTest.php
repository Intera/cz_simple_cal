<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Tests\Unit\ViewHelper;

use stdClass;
use Tx\CzSimpleCal\Tests\Unit\ViewHelpers\IndexedArgumentsTrait;
use Tx\CzSimpleCal\ViewHelpers\UseMicroformatViewHelper;
use TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase;

/**
 * testing the features of the UseMicroformatViewHelper
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class UseMicroformatViewHelperTest extends ViewHelperBaseTestcase
{
    use IndexedArgumentsTrait;

    protected $oldHeaderData = null;

    /**
     * @var UseMicroformatViewHelper
     */
    protected $viewHelper = null;

    public function setUp()
    {
        parent::setUp();

        $this->viewHelper = new UseMicroformatViewHelper();

        $GLOBALS['TSFE'] = new stdClass();
        $GLOBALS['TSFE']->additionalHeaderData = [];
    }

    public function tearDown()
    {
        parent::tearDown();

        unset($GLOBALS['TSFE']);
    }

    public function testCustomUri()
    {
        $uri = 'http://www.example.com/my-microformat';
        $this->initArguments($uri);
        $this->viewHelper->render();

        $changedData = $this->getChangedHeaderData();

        self::assertFalse(empty($changedData) || !is_array($changedData), 'some header data was added');
        self::assertEquals(1, count($changedData), 'exactly one line was added to the header data');

        self::assertContains($uri, current($changedData), 'the correct uri of the microformat was added');

        // Add the same microformat again
        $this->initArguments($uri);
        $this->viewHelper->render();
        $changedData = $this->getChangedHeaderData();
        self::assertEquals(1, count($changedData), 'the same microformat won\'t be added a second time');

        // Add a different microformat
        $this->initArguments($uri . '2');
        $this->viewHelper->render();
        $changedData = $this->getChangedHeaderData();
        self::assertFalse(
            empty($changedData) || !is_array($changedData),
            'headerData is still an array after adding a second microformat'
        );
        self::assertEquals(2, count($changedData), 'a second microformat gets added');
    }

    public function testPredefinedUri()
    {
        $this->initArguments('hcard');
        $this->viewHelper->render();

        $changedData = $this->getChangedHeaderData();

        self::assertFalse(empty($changedData) || !is_array($changedData), 'some header data was added');
        self::assertEquals(1, count($changedData), 'exactly one line was added to the header data');

        self::assertContains('http://microformats.org/', current($changedData), 'known microformats get substituted');
    }

    /**
     * get all entries of an array, with keys that were added during the run of the test
     * (it won't recognized changed content of a key!)
     *
     * @return array
     */
    protected function getChangedHeaderData()
    {
        return $GLOBALS['TSFE']->additionalHeaderData;
    }
}
