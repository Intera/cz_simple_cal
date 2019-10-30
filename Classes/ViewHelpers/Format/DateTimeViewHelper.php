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

use DateTime;
use InvalidArgumentException;
use Tx\CzSimpleCal\Utility\StrToTime;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Formats a unix timestamp to a human-readable, localized string
 *
 * = Examples =
 *
 * <code title="Defaults">
 * <cal:format.dateTime timestamp="1234567890" />
 * </code>
 *
 * Output:
 * 2009-02-13
 *
 *
 * <code title="Defaults with string">
 * <cal:format.dateTime timestamp="2009-02-13 20:31:30GMT" />
 * </code>
 *
 * Output:
 * 2009-02-13
 *
 *
 * <code title="Defaults with DateTime object">
 * <cal:format.dateTime timestamp="dateTimeObject" />
 * </code>
 *
 * Output:
 * 2009-02-13
 *
 *
 * <code title="Custom date format">
 * <cal:format.dateTime format="%a, %e. %B %G" timestamp="1234567890" />
 * </code>
 *
 * Output:
 * Fre, 13. Februar 2009
 * (for german localization)
 *
 *
 * <code title="relative date">
 * <cal:format.dateTime timestamp="1234567890" get="+1 day"/>
 * </code>
 *
 * Output:
 * 2009-02-14
 *
 *
 * <code title="relative date">
 * <cal:format.dateTime timestamp="1234567890" get="first of this month"/>
 * </code>
 *
 * Output:
 * 2009-02-01
 *
 * @see http://www.php.net/manual/en/function.strftime.php
 * @see http://www.php.net/manual/en/function.strtotime.php
 * @see http://www.php.net/manual/en/datetime.formats.relative.php
 */
class DateTimeViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        $this->registerArgument('timestamp', 'mixed', 'unix timestamp, a DateTime object or type "date"', false, null);
        $this->registerArgument('format', 'string', 'Formatting string to be parsed by strftime', false, '%Y-%m-%d');
        $this->registerArgument('get', 'string', 'get some related date (see class doc)', false, '');
    }

    /**
     * Render the supplied unix timestamp in a localized human-readable string.
     *
     * @return string Formatted date
     * @author Christian Zenker <christian.zenker@599media.de>
     */
    public function render(): string
    {
        $timestamp = $this->arguments['timestamp'];
        $format = $this->arguments['format'];
        $get = $this->arguments['get'];

        $timestamp = $this->normalizeTimestamp($timestamp);
        if ($get) {
            $timestamp = $this->modifyDate($timestamp, $get);
        }
        return strftime($format, $timestamp);
    }

    /**
     * do the modification to a relative date
     *
     * @param mixed $timestamp
     * @param string $get
     * @return mixed
     */
    protected function modifyDate($timestamp, string $get)
    {
        return StrToTime::strtotime($get, $timestamp);
    }

    /**
     * handle all the different input formats and return a real timestamp
     *
     * @param mixed $timestamp
     * @return mixed
     * @throws InvalidArgumentException
     */
    protected function normalizeTimestamp($timestamp)
    {
        if (is_null($timestamp)) {
            $timestamp = time();
        } elseif (is_numeric($timestamp)) {
            $timestamp = intval($timestamp);
        } elseif (is_string($timestamp)) {
            $timestamp = StrToTime::strtotime($timestamp);
        } elseif ($timestamp instanceof DateTime) {
            $timestamp = $timestamp->format('U');
        } else {
            throw new InvalidArgumentException(
                sprintf('timestamp might be an integer, a string or a DateTimeObject only.')
            );
        }
        return $timestamp;
    }
}
