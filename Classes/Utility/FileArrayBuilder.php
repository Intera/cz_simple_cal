<?php

namespace Tx\CzSimpleCal\Utility;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * builds an array of file instances from a list of filenames, a path name, alternate texts and captions
 */
class FileArrayBuilder
{
    /**
     * build an array of image instances
     *
     * @param string|array $files string should use "," as seperator
     * @param string $path
     * @param array|string $alternates string should use newline as seperator
     * @param string|array $captions string should use newline as seperator
     * @return \Tx\CzSimpleCal\Domain\Model\File[]
     */
    public static function build($files, $path, $alternates = '', $captions = '')
    {
        $return = [];

        if (!is_array($files)) {
            $files = is_string($files) && !empty($files) ? GeneralUtility::trimExplode(',', $files, false) : [];
        }

        if (!is_array($alternates)) {
            $alternates = is_string($alternates) && !empty($alternates) ? GeneralUtility::trimExplode(
                "\n",
                $alternates,
                false
            ) : [];
        }

        if (!is_array($captions)) {
            $captions = is_string($captions) && !empty($captions) ? GeneralUtility::trimExplode(
                "\n",
                $captions,
                false
            ) : [];
        }

        if ($path && substr($path, -1) !== '/') {
            $path = $path . '/';
        }

        foreach ($files as $key => $fileName) {
            if (empty($fileName)) {
                continue;
            }
            $file = new \Tx\CzSimpleCal\Domain\Model\File();
            $file->setPath($path);
            $file->setFile($fileName);
            if (array_key_exists($key, $captions) && $captions[$key]) {
                $file->setCaption($captions[$key]);
            }
            if (array_key_exists($key, $alternates) && $alternates[$key]) {
                $file->setAlternateText($alternates[$key]);
            }
            $return[] = $file;
        }

        return $return;
    }

    /**
     * Builds an array of file domain models for the given file references.
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $files
     * @return \Tx\CzSimpleCal\Domain\Model\File[]
     */
    public static function buildFromReferences(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $files)
    {
        $filesArray = [];

        /**
         * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference $fileReference
         * @var \Tx\CzSimpleCal\Domain\Model\File $file
         */
        foreach ($files as $fileReference) {
            $file = new \Tx\CzSimpleCal\Domain\Model\File();
            $originalReference = $fileReference->getOriginalResource();
            $file->setPathAndFilename($originalReference->getOriginalFile()->getPublicUrl());
            $file->setFile($originalReference->getOriginalFile()->getName());
            $file->setAlternateText($originalReference->getAlternative());
            $file->setCaption($originalReference->getDescription());
            $filesArray[] = $file;
        }

        return $filesArray;
    }
}
