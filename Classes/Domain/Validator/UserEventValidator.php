<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Domain\Validator;

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

use Tx\CzSimpleCal\Utility\StrToTime;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\Validation\Validator\ConjunctionValidator;
use TYPO3\CMS\Extbase\Validation\Validator\DisjunctionValidator;
use TYPO3\CMS\Extbase\Validation\Validator\GenericObjectValidator;
use TYPO3\CMS\Extbase\Validation\Validator\StringLengthValidator;

/**
 * validates an event submitted by a user
 */
class UserEventValidator extends GenericObjectValidator
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager = null;

    public function injectObjectManager(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * validate an Event submitted by the user
     *
     * @param mixed $value
     * @return void
     */
    public function isValid($value)
    {
        $validator = $this->objectManager->get(
            StringLengthValidator::class,
            [
                'minimum' => 3,
                'maximum' => 255,
            ]
        );
        $this->addPropertyValidator('title', $validator);

        // Start day
        $validator = $this->objectManager->get(
            DateValidator::class,
            [
                'object' => $value,
                'propertyName' => 'startDay',
                'required' => true,
                'minimum' => StrToTime::strtotime('midnight'),
            ]
        );
        $this->addPropertyValidator('startDay', $validator);

        // Start time
        $validator = $this->objectManager->get(
            TimeValidator::class,
            [
                'object' => $value,
                'propertyName' => 'startTime',
                'required' => false,
            ]
        );
        $this->addPropertyValidator('startTime', $validator);

        // End day
        $validator = $this->objectManager->get(
            DateValidator::class,
            [
                'object' => $value,
                'propertyName' => 'endDay',
                'required' => false,
                'minimum' => StrToTime::strtotime('midnight'),
            ]
        );
        $this->addPropertyValidator('endDay', $validator);

        // End time
        /** @var TimeValidator $validator */
        $validator = $this->objectManager->get(
            TimeValidator::class,
            [
                'object' => $value,
                'propertyName' => 'endTime',
                'required' => false,
            ]
        );
        $this->addPropertyValidator('endTime', $validator);

        // Description
        $validator = $this->objectManager->get(DisjunctionValidator::class);
        $noTagsValidator = $this->objectManager->get(NoTagsValidator::class);
        $validator->addValidator($noTagsValidator);
        $emptyValidator = $this->objectManager->get(EmptyValidator::class);
        $validator->addValidator($emptyValidator);
        $this->addPropertyValidator('description', $validator);

        // LocationName
        $validator = $this->objectManager->get(DisjunctionValidator::class);
        $stringValidator = $this->objectManager->get(
            StringLengthValidator::class,
            [
                'minimum' => 3,
                'maximum' => 255,
            ]
        );
        $validator->addValidator($stringValidator);
        $emptyValidator = $this->objectManager->get(EmptyValidator::class);
        $validator->addValidator($emptyValidator);
        $this->addPropertyValidator('locationName', $validator);

        // LocationAddress
        $validator = $this->objectManager->get(DisjunctionValidator::class);
        $stringValidator = $this->objectManager->get(
            StringLengthValidator::class,
            [
                'minimum' => 3,
                'maximum' => 255,
            ]
        );
        $validator->addValidator($stringValidator);
        $emptyValidator = $this->objectManager->get(EmptyValidator::class);
        $validator->addValidator($emptyValidator);
        $this->addPropertyValidator('locationAddress', $validator);

        // Location city
        $validator = $this->objectManager->get(DisjunctionValidator::class);
        /** @var StringLengthValidator $stringValidator */
        $stringValidator = $this->objectManager->get(
            StringLengthValidator::class,
            [
                'minimum' => 3,
                'maximum' => 255,
            ]
        );
        $validator->addValidator($stringValidator);
        $emptyValidator = $this->objectManager->get(EmptyValidator::class);
        $validator->addValidator($emptyValidator);
        $this->addPropertyValidator('locationCity', $validator);

        // Show page instead
        $validator = $this->objectManager->get(DisjunctionValidator::class);
        $andValidator = $this->objectManager->get(ConjunctionValidator::class);
        $stringValidator = $this->objectManager->get(
            StringLengthValidator::class,
            [
                'minimum' => 10,
                'maximum' => 255,
            ]
        );
        $andValidator->addValidator($stringValidator);
        $urlValidator = $this->objectManager->get(
            UrlValidator::class,
            [
                'object' => $value,
                'propertyName' => 'showPageInstead',
            ]
        );
        $andValidator->addValidator($urlValidator);
        $validator->addValidator($andValidator);
        $emptyValidator = $this->objectManager->get(EmptyValidator::class);
        $validator->addValidator($emptyValidator);
        $this->addPropertyValidator('showPageInstead', $validator);

        // Twitter hashtags
        $validator = $this->objectManager->get(
            TwitterHashtagValidator::class,
            [
                'object' => $value,
                'propertyName' => 'twitterHashtags',
                'required' => false,
            ]
        );
        $this->addPropertyValidator('twitterHashtags', $validator);

        // Flickr tags
        $validator = $this->objectManager->get(
            FlickrTagValidator::class,
            [
                'object' => $value,
                'propertyName' => 'flickrTags',
                'required' => false,
            ]
        );
        $this->addPropertyValidator('flickrTags', $validator);

        parent::isValid($value);

        // Check: event does not end before it starts
        if ($value->getDateTimeObjectStart()->getTimestamp() > $value->getDateTimeObjectEnd()->getTimestamp()) {
            $this->addError('This event is not allowed to start before it ends.', 1316261470);
        }

        // Prevent descriptions from having tags (will be parsed with parsefunc_RTE
        $value->setDescription(htmlspecialchars($value->getDescription(), null, null, false));
    }
}
