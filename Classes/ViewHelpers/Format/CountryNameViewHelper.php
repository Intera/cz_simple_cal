<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\ViewHelpers\Format;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Christian Zenker <christian.zenker@599media.de>, 599media GmbH
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
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Psr\Log\LoggerInterface;
use SJBR\StaticInfoTables\PiBaseApi;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * format a localized name of a country by its isoCode
 */
class CountryNameViewHelper extends AbstractViewHelper
{
    /**
     * @var PiBaseApi
     */
    protected static $staticInfoObject = null;

    public function initializeArguments()
    {
        $this->registerArgument(
            'isoCode',
            'string',
            'the three-letter isocode of the country (as given by static_info_tables)',
            true
        );
    }

    /**
     * @return LoggerInterface
     */
    protected static function getLogger()
    {
        return GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
    }

    /**
     * init static info tables to use with this view helper
     */
    protected static function init()
    {
        if (!is_null(self::$staticInfoObject)) {
            return;
        }

        if (!ExtensionManagementUtility::isLoaded('static_info_tables')) {
            self::$staticInfoObject = false;
            /** @noinspection PhpUndefinedConstantInspection */
            self::getLogger()->warning('static_info_tables needs to be installed to use ' . get_class(self));
            return;
        }

        self::$staticInfoObject = GeneralUtility::makeInstance(PiBaseApi::class);

        if (self::$staticInfoObject->needsInit()) {
            self::$staticInfoObject->init();
        }
    }

    /**
     * Get the localized name of a country from its country code
     *
     * @return string localized country name
     * @author Christian Zenker <christian.zenker@599media.de>
     */
    public function render(): string
    {
        $isoCode = trim($this->arguments['isoCode']);
        if ($isoCode === '') {
            return '';
        }

        self::init();

        // If init went wrong
        if (!self::$staticInfoObject) {
            return $isoCode;
        }

        return self::$staticInfoObject->getStaticInfoName('COUNTRIES', $isoCode);
    }
}
