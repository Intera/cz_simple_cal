<?php

namespace Tx\CzSimpleCal\Tests\Selenium;

use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class BaseSeleniumTestCase extends tx_selenium_testcase
{
    protected $browser;

    protected $browserUrl;

    public function setUp()
    {
        if (is_null($this->browserUrl)) {
            $this->browserUrl = $this->guessBrowserUrl();
        }
        $this->initializeSelenium(
            is_null($this->browser) ? '*firefox' : $this->browser,
            $this->browserUrl
        );
    }

    public function tearDown()
    {
        try {
            $this->selenium->stop();
        } catch (Testing_Selenium_Exception $e) {
            echo $e;
        }
    }

    public function assertElementNotPresent($locator, $message = null)
    {
        $this->assertFalse(
            $this->selenium->isElementPresent($locator),
            is_null($message) ? $message : sprintf('"assert element is not present: %s"', $locator)
        );
    }

    public function assertElementPresent($locator, $message = null)
    {
        $this->assertTrue(
            $this->selenium->isElementPresent($locator),
            is_null($message) ? $message : sprintf('"assert element present: %s"', $locator)
        );
    }

    public function assertTextNotPresent($pattern, $message = null)
    {
        $this->assertFalse(
            $this->selenium->isTextPresent($pattern),
            is_null($message) ? $message : sprintf('"assert text is not present: %s"', $pattern)
        );
    }

    public function assertTextPresent($pattern, $message = null)
    {
        $this->assertTrue(
            $this->selenium->isTextPresent($pattern),
            is_null($message) ? $message : sprintf('"assert text present: %s"', $pattern)
        );
    }

    protected function guessBrowserUrl()
    {
        $url = GeneralUtility::getThisUrl();
        $pos = strpos($url, 'typo3/');
        if ($pos === false) {
            throw new \RuntimeException('Could not determine the url for the frontend.');
        }
        return 'http://' . substr($url, 0, $pos);
    }
}
