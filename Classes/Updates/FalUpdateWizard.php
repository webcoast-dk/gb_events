<?php
namespace GuteBotschafter\GbEvents\Updates;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Johannes Hertenstein, NIMIUS <johannes@nimius.net>
 *  (c) 2015 Morton Jonuschat, Gute Botschafter GmbH <m.jonuschat@gute-botschafter.de>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\AbstractUpdate;

class FalUpdateWizard extends AbstractUpdate
{
    const UPLOADS_FOLDER = "uploads/tx_gbevents/";
    const FAL_FOLDER_IMAGES = "_migrated/gbevents_images";
    const FAL_FOLDER_DOWNLOADS = "_migrated/gbevents_downloads";

    /**
     * @var string
     */
    protected $title = 'Migrate file relations of EXT:gb_events';

    /**
     * @var string
     */
    protected $imageDirectory;

    /**
     * @var string
     */
    protected $downloadDirectory;

    /**
     * @var \TYPO3\CMS\Core\Resource\ResourceFactory
     */
    protected $fileFactory;

    /**
     * @var \TYPO3\CMS\Core\Resource\FileRepository
     */
    protected $fileRepository;

    /**
     * @var \TYPO3\CMS\Core\Resource\ResourceStorage
     */
    protected $storage;

    /**
     * Initialize all required repository and factory objects.
     *
     * @throws \RuntimeException
     */
    protected function init()
    {
        $fileadminDirectory = rtrim($GLOBALS['TYPO3_CONF_VARS']['BE']['fileadminDir'], '/') . '/';
        /** @var $storageRepository \TYPO3\CMS\Core\Resource\StorageRepository */
        $storageRepository = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\StorageRepository');
        $storages = $storageRepository->findAll();
        foreach ($storages as $storage) {
            $storageRecord = $storage->getStorageRecord();
            $configuration = $storage->getConfiguration();
            $isLocalDriver = $storageRecord['driver'] === 'Local';
            $isOnFileadmin = !empty($configuration['basePath'])
                && GeneralUtility::isFirstPartOfStr($configuration['basePath'], $fileadminDirectory);
            if ($isLocalDriver && $isOnFileadmin) {
                $this->storage = $storage;
                break;
            }
        }
        if (!isset($this->storage)) {
            throw new \RuntimeException(
                'Local default storage could not be initialized - might be due to missing sys_file* tables.'
            );
        }
        $this->fileFactory = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\ResourceFactory');
        $this->fileRepository = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\FileRepository');
        $this->imageDirectory = PATH_site . $fileadminDirectory . self::FAL_FOLDER_IMAGES . '/';
        $this->downloadDirectory = PATH_site . $fileadminDirectory . self::FAL_FOLDER_DOWNLOADS . '/';
    }

    /**
     * Checks if an update is needed
     *
     * @param string &$description : The description for the update
     * @return boolean TRUE if an update is needed, FALSE otherwise
     */
    public function checkForUpdate(&$description)
    {
        $updateNeeded = false;
        // Fetch records where the old relation is used and the new one is empty
        $imageRows = $this->getDatabaseConnection()->exec_SELECTcountRows(
            'uid',
            'tx_gbevents_domain_model_event',
            "images != CAST(CAST(images AS UNSIGNED INTEGER) AS CHAR) AND images != '' AND deleted = 0"
        );
        if ($imageRows > 0) {
            $description = 'There are <b>'
                . $imageRows
                . '</b> events which are using the old image file upload. ';
            $description .= 'This wizard will move the files to "fileadmin/' . self::FAL_FOLDER_IMAGES . '".<br />';
            $updateNeeded = true;
        }

        $downloadRows = $this->getDatabaseConnection()->exec_SELECTcountRows(
            'uid',
            'tx_gbevents_domain_model_event',
            "downloads != CAST(CAST(downloads AS UNSIGNED INTEGER) AS CHAR) AND downloads != '' AND deleted = 0"
        );
        if ($downloadRows > 0) {
            $description .= 'There are <b>'
                . $downloadRows
                . '</b> downloads which are using the old dowload file upload. ';
            $description .= 'This wizard will move the files to "fileadmin/' . self::FAL_FOLDER_DOWNLOADS . '".<br />';
            $updateNeeded = true;
        }

        if ($updateNeeded) {
            $description .= '<b>Important:</b> The <b>first</b> local storage inside "'
                . $GLOBALS['TYPO3_CONF_VARS']['BE']['fileadminDir'] . '"';
            $description .= ' will be used for the migration.'
                . ' If you have multiple storages, only enable the one which should be used for the migration.';
        }

        return $updateNeeded;
    }

    /**
     * Performs the database update.
     *
     * @param array &$dbQueries Queries done in this update
     * @param mixed &$customMessages Custom messages
     * @return boolean TRUE on success, FALSE on error
     */
    public function performUpdate(array &$dbQueries, &$customMessages)
    {
        $this->init();
        $this->checkPrerequisites();

        $imageRecords = $this->getEventRecords('images');
        if ($imageRecords) {
            foreach ($imageRecords as $singleRecord) {
                $this->migrateRecord($singleRecord, 'images');
            }
            $this->setCountInEventRecord('images');
        }

        $downloadRecords = $this->getEventRecords('downloads');
        if ($downloadRecords) {
            foreach ($downloadRecords as $singleRecord) {
                $this->migrateRecord($singleRecord, 'downloads');
            }
            $this->setCountInEventRecord('downloads');
        }

        return true;
    }

    /**
     * Processes the actual transformation from CSV to sys_file_references
     *
     * @param array $record
     * @param string $field
     * @return void
     */
    protected function migrateRecord(array $record, $field)
    {
        $files = GeneralUtility::trimExplode(', ', $record[$field], true);
        if (!empty($files)) {
            if ($field === 'images') {
                $targetDirectory = $this->imageDirectory;
                $folder = self::FAL_FOLDER_IMAGES;
            } else {
                $targetDirectory = $this->downloadDirectory;
                $folder = self::FAL_FOLDER_DOWNLOADS;
            }

            foreach ($files as $index => $file) {
                if (file_exists(PATH_site . self::UPLOADS_FOLDER . $file)) {
                    GeneralUtility::upload_copy_move(
                        PATH_site . self::UPLOADS_FOLDER . $file,
                        $targetDirectory . $file
                    );

                    /** @var \TYPO3\CMS\Core\Resource\File $fileObject */
                    $fileObject = $this->storage->getFile($folder . '/' . $file);
                    $this->fileRepository->add($fileObject);
                    $dataArray = [
                        'uid_local' => $fileObject->getUid(),
                        'tablenames' => 'tx_gbevents_domain_model_event',
                        'fieldname' => $field,
                        'uid_foreign' => $record['uid'],
                        'table_local' => 'sys_file',
                        // the sys_file_reference record should always placed on the same page
                        // as the record to link to, see issue #46497
                        'cruser_id' => 45721,
                        'pid' => $record['pid'],
                        'sorting_foreign' => 256 * ($index + 1),
                        'title' => '',
                        'hidden' => 0,
                    ];

                    $this->getDatabaseConnection()->exec_INSERTquery('sys_file_reference', $dataArray);
                }
            }
        }
    }

    /**
     * Update the events table and set the count of relations
     *
     * @param string $field
     * @return void
     */
    protected function setCountInEventRecord($field)
    {
        $rows = $this->getDatabaseConnection()->exec_SELECTgetRows(
            'count(*) as count, uid_foreign as uid',
            'sys_file_reference',
            "deleted=0 AND hidden=0 AND cruser_id=45721 AND fieldname= '"
            . $field
            . "' AND tablenames= 'tx_gbevents_domain_model_event'",
            'uid_foreign'
        );

        foreach ($rows as $row) {
            $this->getDatabaseConnection()->exec_UPDATEquery(
                'tx_gbevents_domain_model_event',
                'uid=' . (int)$row['uid'],
                [$field => $row['count']]
            );
        }
    }

    /**
     * Ensures the folders for migrated downloads and images are available.
     *
     * @return void
     */
    protected function checkPrerequisites()
    {
        if (!$this->storage->hasFolder(self::FAL_FOLDER_IMAGES)) {
            $this->storage->createFolder(self::FAL_FOLDER_IMAGES, $this->storage->getRootLevelFolder());
        }
        if (!$this->storage->hasFolder(self::FAL_FOLDER_DOWNLOADS)) {
            $this->storage->createFolder(self::FAL_FOLDER_DOWNLOADS, $this->storage->getRootLevelFolder());
        }
    }

    /**
     * Retrieve every record which needs to be processed
     *
     * @param $field string field name
     * @return array
     */
    protected function getEventRecords($field)
    {
        $records = $this->getDatabaseConnection()->exec_SELECTgetRows(
            '*',
            'tx_gbevents_domain_model_event',
            $field . ' != CAST(CAST('
            . $field
            . ' AS UNSIGNED INTEGER) AS CHAR) AND '
            . $field . " != '' AND deleted = 0"
        );

        return $records;
    }

    /**
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
