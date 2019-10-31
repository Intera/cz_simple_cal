<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Domain\Model\Enumeration;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "cz_simple_cal".            *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\CMS\Core\Type\Enumeration;

/**
 * The time type of an event.
 */
class EventTimeType extends Enumeration
{
    /**
     * Start date and end time: 14.02.2014 - 15.02.2014
     *
     * @const
     */
    const ALL_DATES = 'allDates';

    /**
     * Start date / time and end date / time: 14.02.2014 15:00 Uhr - 15.02.2014 16:00 Uhr
     *
     * @const
     */
    const ALL_DATE_TIMES = 'allDateTimes';

    /**
     * Only a start date: 14.02.2014
     *
     * @const
     */
    const START_DATE = 'startDate';

    /**
     * Only start date and time: 14.02.2014 14:00 Uhr
     *
     * @const
     */
    const START_DATE_TIME = 'startDateTime';

    /**
     * Start date and start and end time: 14.02.2014 14:00 - 16:00 Uhr
     *
     * @const
     */
    const START_DATE_TIME_AND_END_TIME = 'startDateTimeAndEndTime';
}
