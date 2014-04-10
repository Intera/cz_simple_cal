<?php
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

use TYPO3\CMS\Extbase\Validation\Validator\GenericObjectValidator;

/**
 * validates an event submitted by a user
 */
class UserEventValidator extends GenericObjectValidator {

	/**
	 * validate an Event submitted by the user
	 *
	 * @param mixed $value
	 * @return void
	 */
	public function isValid($value) {

		/** @var \TYPO3\CMS\Extbase\Validation\Validator\StringLengthValidator $validator */
		$validator = $this->getObjectManager()->get(
			'TYPO3\\CMS\\Extbase\\Validation\\Validator\\StringLengthValidator',
			array('minimum' => 3, 'maximum' => 255)
		);
		$this->addPropertyValidator('title', $validator);

		// startDay
		/** @var DateValidator $validator */
		$validator = $this->getObjectManager()->get(
			'Tx\\CzSimpleCal\\Domain\\Validator\\DateValidator',
			array(
				'object' => $value,
				'propertyName' => 'startDay',
				'required' => true,
				'minimum' => \Tx\CzSimpleCal\Utility\StrToTime::strtotime('midnight')
			)
		);
		$this->addPropertyValidator('startDay', $validator);

		// startTime
		/** @var TimeValidator $validator */
		$validator = $this->getObjectManager()->get(
			'Tx\\CzSimpleCal\\Domain\\Validator\\TimeValidator',
			array(
				'object' => $value,
				'propertyName' => 'startTime',
				'required' => false
			)
		);
		$this->addPropertyValidator('startTime', $validator);

		// endDay
		/** @var DateValidator $validator */
		$validator = $this->getObjectManager()->get(
			'Tx\\CzSimpleCal\\Domain\\Validator\\DateValidator',
			array(
				'object' => $value,
				'propertyName' => 'endDay',
				'required' => false,
				'minimum' => \Tx\CzSimpleCal\Utility\StrToTime::strtotime('midnight')
			)
		);
		$this->addPropertyValidator('endDay', $validator);

		// endTime
		/** @var TimeValidator $validator */
		$validator = $this->getObjectManager()->get(
			'Tx\\CzSimpleCal\\Domain\\Validator\\TimeValidator',
			array(
				'object' => $value,
				'propertyName' => 'endTime',
				'required' => false
			)
		);
		$this->addPropertyValidator('endTime', $validator);

		// description
		/** @var \TYPO3\CMS\Extbase\Validation\Validator\DisjunctionValidator $validator */
		$validator = $this->getObjectManager()->get('TYPO3\\CMS\\Extbase\\Validation\\Validator\\DisjunctionValidator');
		/** @var NoTagsValidator $noTagsValidator */
		$noTagsValidator = $this->getObjectManager()->get('Tx\\CzSimpleCal\\Domain\\Validator\\NoTagsValidator');
		$validator->addValidator($noTagsValidator);
		/** @var EmptyValidator $emptyValidator */
		$emptyValidator = $this->getObjectManager()->get('Tx\\CzSimpleCal\\Domain\\Validator\\EmptyValidator');
		$validator->addValidator($emptyValidator);
		$this->addPropertyValidator('description', $validator);

		// locationName
		/** @var \TYPO3\CMS\Extbase\Validation\Validator\DisjunctionValidator $validator */
		$validator = $this->getObjectManager()->get('TYPO3\\CMS\\Extbase\\Validation\\Validator\\DisjunctionValidator');
		/** @var \TYPO3\CMS\Extbase\Validation\Validator\StringLengthValidator $stringValidator */
		$stringValidator = $this->getObjectManager()->get(
			'TYPO3\\CMS\\Extbase\\Validation\\Validator\\StringLengthValidator',
			array('minimum' => 3, 'maximum' => 255)
		);
		$validator->addValidator($stringValidator);
		/** @var EmptyValidator $emptyValidator */
		$emptyValidator = $this->getObjectManager()->get('Tx\\CzSimpleCal\\Domain\\Validator\\EmptyValidator');
		$validator->addValidator($emptyValidator);
		$this->addPropertyValidator('locationName', $validator);

		// locationAddress
		/** @var \TYPO3\CMS\Extbase\Validation\Validator\DisjunctionValidator $validator */
		$validator = $this->getObjectManager()->get('TYPO3\\CMS\\Extbase\\Validation\\Validator\\DisjunctionValidator');
		/** @var \TYPO3\CMS\Extbase\Validation\Validator\StringLengthValidator $stringValidator */
		$stringValidator = $this->getObjectManager()->get(
			'TYPO3\\CMS\\Extbase\\Validation\\Validator\\StringLengthValidator',
			array('minimum' => 3, 'maximum' => 255)
		);
		$validator->addValidator($stringValidator);
		$emptyValidator = $this->getObjectManager()->get('Tx\\CzSimpleCal\\Domain\\Validator\\EmptyValidator');
		$validator->addValidator($emptyValidator);
		$this->addPropertyValidator('locationAddress', $validator);

		// locationCity
		/** @var \TYPO3\CMS\Extbase\Validation\Validator\DisjunctionValidator $validator */
		$validator = $this->getObjectManager()->get('TYPO3\\CMS\\Extbase\\Validation\\Validator\\DisjunctionValidator');
		/** @var \TYPO3\CMS\Extbase\Validation\Validator\StringLengthValidator $stringValidator */
		$stringValidator = $this->getObjectManager()->get(
			'TYPO3\\CMS\\Extbase\\Validation\\Validator\\StringLengthValidator',
			array('minimum' => 3, 'maximum' => 255)
		);
		$validator->addValidator($stringValidator);
		$emptyValidator = $this->getObjectManager()->get('Tx\\CzSimpleCal\\Domain\\Validator\\EmptyValidator');
		$validator->addValidator($emptyValidator);
		$this->addPropertyValidator('locationCity', $validator);

		// showPageInstead
		/** @var \TYPO3\CMS\Extbase\Validation\Validator\DisjunctionValidator $validator */
		$validator = $this->getObjectManager()->get('TYPO3\\CMS\\Extbase\\Validation\\Validator\\DisjunctionValidator');
		/** @var \TYPO3\CMS\Extbase\Validation\Validator\ConjunctionValidator $andValidator */
		$andValidator = $this->getObjectManager()->get('TYPO3\\CMS\\Extbase\\Validation\\Validator\\ConjunctionValidator');
		$stringValidator = $this->getObjectManager()->get(
			'TYPO3\\CMS\\Extbase\\Validation\\Validator\\StringLengthValidator',
			array('minimum' => 10, 'maximum' => 255)
		);
		$andValidator->addValidator($stringValidator);
		/** @var UrlValidator $urlValidator */
		$urlValidator = $this->getObjectManager()->get(
			'Tx\\CzSimpleCal\\Domain\\Validator\\UrlValidator',
			array(
				'object' => $value,
				'propertyName' => 'showPageInstead'
			)
		);
		$andValidator->addValidator($urlValidator);
		$validator->addValidator($andValidator);
		/** @var EmptyValidator $emptyValidator */
		$emptyValidator = $this->getObjectManager()->get('Tx\\CzSimpleCal\\Domain\\Validator\\EmptyValidator');
		$validator->addValidator($emptyValidator);
		$this->addPropertyValidator('showPageInstead', $validator);

		// twitterHashtags
		/** @var TwitterHashtagValidator $validator */
		$validator = $this->getObjectManager()->get(
			'Tx\\CzSimpleCal\\Domain\\Validator\\TwitterHashtagValidator',
			array(
				'object' => $value,
				'propertyName' => 'twitterHashtags',
				'required' => false,
			)
		);
		$this->addPropertyValidator('twitterHashtags', $validator);


		// flickrTags
		/** @var FlickrTagValidator $validator */
		$validator = $this->getObjectManager()->get(
			'Tx\\CzSimpleCal\\Domain\\Validator\\FlickrTagValidator',
			array(
				'object' => $value,
				'propertyName' => 'flickrTags',
				'required' => false,
			)
		);
		$this->addPropertyValidator('flickrTags', $validator);

		parent::isValid($value);

		// check: event does not end before it starts
		if($value->getDateTimeObjectStart()->getTimestamp() > $value->getDateTimeObjectEnd()->getTimestamp()) {
			$this->addError('This event is not allowed to start before it ends.', 1316261470);
		}

		// prevent descriptions from having tags (will be parsed with parsefunc_RTE
		$value->setDescription(htmlspecialchars($value->getDescription(), null, null, false));
	}


	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 */
	protected $objectManager = null;

	/**
	 * @return \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 */
	protected function getObjectManager() {
		if(is_null($this->objectManager)) {
			$this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		}
		return $this->objectManager;
	}
}