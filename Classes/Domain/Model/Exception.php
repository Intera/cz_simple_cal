<?php
/** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
/** @noinspection PhpFullyQualifiedNameUsageInspection */

declare(strict_types=1);

namespace Tx\CzSimpleCal\Domain\Model;

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

use Tx\CzSimpleCal\Domain\Model\Enumeration\ExceptionType;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * An exception for an event in the calendar
 */
class Exception extends BaseEvent
{
    /**
     * @Extbase\ORM\Transient
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Status of the event.
     *
     * @var \Tx\CzSimpleCal\Domain\Model\Enumeration\EventStatus
     */
    protected $status;

    /**
     * @var string
     */
    protected $teaser;

    /**
     * The title of this exception
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     */
    protected $title;

    /**
     * @var \Tx\CzSimpleCal\Domain\Model\Enumeration\ExceptionType
     */
    protected $type;

    public function injectObjectManager(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return (string)$this->status;
    }

    /**
     * @return string
     */
    public function getTeaser()
    {
        return $this->teaser;
    }

    /**
     * Getter for title
     *
     * @return string The title of this exception
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return Enumeration\ExceptionType
     */
    public function getType()
    {
        if (!isset($this->type)) {
            $this->type = $this->objectManager->get(ExceptionType::class);
        }
        return $this->type;
    }

    /**
     * Setter for title
     *
     * @param string $title The title of this exception
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
}
