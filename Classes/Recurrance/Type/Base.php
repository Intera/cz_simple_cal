<?php

namespace Tx\CzSimpleCal\Recurrance\Type;

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

use Tx\CzSimpleCal\Domain\Interfaces\IsRecurring;
use Tx\CzSimpleCal\Recurrance\Timeline\Base as TimelineBase;

/**
 * Base class for all recurrance types.
 */
abstract class Base
{
    /**
     * @var IsRecurring
     */
    protected $event = null;

    /**
     * @var TimelineBase
     */
    protected $timeline = null;

    abstract protected function doBuild();

    /**
     * build all recurrant events from an Event
     *
     * @param IsRecurring $event
     * @param TimelineBase $timeline
     * @return TimelineBase
     */
    public function build(IsRecurring $event, $timeline)
    {
        $this->event = $event;
        $this->timeline = $timeline;

        $this->doBuild();

        return $this->timeline;
    }

    /**
     * add locallang labels to an array of subtypes
     *
     * @param array $values
     * @return array
     */
    protected function addLL($values)
    {
        $base = 'LLL:EXT:cz_simple_cal/Resources/Private/Language/locallang_db.xml:'
            . 'tx_czsimplecal_domain_model_event.recurrance_subtype.';
        foreach ($values as &$value) {
            $value = [
                $this->getLanguageService()->sL($base . $value),
                $value,
            ];
        }
        return $values;
    }

    /**
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }
}
