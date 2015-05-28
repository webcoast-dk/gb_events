<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Johannes Hertenstein, NIMIUS <johannes@nimius.net>
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
use GuteBotschafter\GbEvents\Domain\Repository\EventRepository;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Query;

/**
 *
 *
 * @package TYPO3
 * @subpackage GuteBotschafter\GbEvents
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class ext_update {
	const UPLOADS_FOLDER = "uploads/tx_gbevents/";
	const GLUE = ",";
	const FAL_FOLDER_IMAGES = "gbevents/images";
	const FAL_FOLDER_DOWNLOADS = "gbevents/downloads/";

	const IMAGES = "images";
	const DOWNLOADS = "downloads";

	const TABLE = "tx_gbevents_domain_model_event";

	/**
	 * @var ObjectManager
	 */
	protected $objectManager;

	/**
	 * @var StorageRepository
	 */
	protected $storageRepository;

	/**
	 * @var Folder
	 */
	protected $imageFolder;

	/**
	 * @var Folder
	 */
	protected $downloadsFolder;

	/**
	 * @var ResourceStorage
	 */
	protected $storage;

	/**
	 * @var EventRepository;
	 */
	protected $eventRepository;

	/**
	 * @var PersistenceManager
	 */
	protected $persistenceManager;

	/**
	 * @var array
	 */
	protected $log = array();

	/**
	 * Constructor - sets up all the repositories and stuff
	 */
	public function __construct(){
		$this->objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		$this->storageRepository = $this->objectManager->get("TYPO3\\CMS\\Core\\Resource\\StorageRepository");
		$this->storage = $this->storageRepository->findByUid(1);
		$this->eventRepository   = $this->objectManager->get("GuteBotschafter\\GbEvents\\Domain\\Repository\\EventRepository");
		$this->imageFolder = $this->createFalFolder(self::FAL_FOLDER_IMAGES);
		$this->downloadsFolder = $this->createFalFolder(self::FAL_FOLDER_DOWNLOADS);
		$this->persistenceManager =  GeneralUtility::makeInstance("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
	}

	/**
	 * creates a folder in the FAL, if non existent
	 *
	 * @param string $path
	 * @return Folder
	 */
	protected function createFalFolder($path){
		$path = explode("/", $path);
		$folder = $this->storage->getRootLevelFolder();
		foreach($path as $p){
			if($folder->hasFolder($p)){
				$folder = $folder->getSubfolder($p);
			} else {
				$folder = $folder->createFolder($p);
			}
		}
		return $folder;
	}

	/**
	 * Called by the extension manager to determine if the update menu entry
	 * should by showed.
	 *
	 * @return bool
	 * @todo find a better way to determine if update is needed or not.
	 */
	public function access() {
		return TRUE;
	}

	/**
	 * Main update function - this is where the magic happens
	 * @return string
	 * @throws Exception
	 * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
	 */
	public function main(){
		/** @var Query $query */
		$query = $this->eventRepository->createQuery();
		$querySettings = $query->getQuerySettings()
				->setIgnoreEnableFields(true)
				->setRespectStoragePage(false)
				->setRespectSysLanguage(false)
				->setIncludeDeleted(true);
		$query->setQuerySettings($querySettings); // we want ALL of the events
		$query->statement("SELECT uid, pid, downloads, images FROM ".self::TABLE);


		foreach($query->execute(true) as $e){
			/** @var Array $e */

			if(!empty($e["images"])){
				$falImages = $this->files2FAL(explode(self::GLUE, $e["images"]), $this->imageFolder);
				foreach($falImages as $img){
					$this->FAL2FileReference($img, $e, self::IMAGES);
				}
			}
			if(!empty($e["downloads"])){
				$falDownloads = $this->files2FAL(explode(self::GLUE, $e["downloads"]), $this->downloadsFolder);
				foreach($falDownloads as $dl){
					$this->FAL2FileReference($dl, $e, self::DOWNLOADS);
				}
			}
		}

		return implode("<br/>", $this->log);
	}

	/**
	 * converts the array of filenames to FAL Files and returns them as an Array
	 * @param String[] 	$filenamearr
	 * @param Folder 	$falfolder
	 * @return File[]
	 * @throws Exception
	 * @throws \TYPO3\CMS\Core\Resource\Exception\ExistingTargetFileNameException
	 */
	private function files2FAL($filenamearr, $falfolder){
		$retval = array();

		foreach($filenamearr as $filename){
			$path = GeneralUtility::getFileAbsFileName(self::UPLOADS_FOLDER . $filename);

			if(file_exists($path)){
				$sha = sha1_file($path);

				/** @var File $falFile */
				$falFile = $this->storage->addFile($path, $falfolder, $filename);

				// check if the file is the same
				// abort, if not
				if($falFile->getSha1() !== $sha){
					throw new \Exception("Files were altered during fal transition, aborting ({$falFile->getSha1()} does not match {$sha})");
				}

				$retval[] = $falFile;
				$this->log[] = self::UPLOADS_FOLDER."{$filename} -> fileadmin/{$falFile->getIdentifier()}";
			} else {
				$this->log[] = "<span style=\"color: red\"> File {$path} does not exist </span>";
			}

		}
		return $retval;
	}

	/**
	 * Creates a file reference from a File, see
	 * http://wiki.typo3.org/File_Abstraction_Layer
	 *
	 * @param File $file
	 * @param Array $event
	 * @param String $type
	 * @throws Exception
	 */
	private function FAL2FileReference(File $file, Array $event, $type){
		$data = array();
		$data['sys_file_reference']['NEW1234'] = array(
				'uid_local' => $file->getUid(),
				'uid_foreign' => $event["uid"], // uid of your content record
				'tablenames' => self::TABLE,
				'fieldname' => $type,
				'pid' => $event["pid"], // parent id of the parent page
				'table_local' => 'sys_file',
		);
		$data[self::TABLE][$event["uid"]] = array($type => 'NEW1234');

		/** @var DataHandler $tce */
		$tce = GeneralUtility::makeInstance('TYPO3\CMS\Core\DataHandling\DataHandler');
		$tce->start($data, array(), $GLOBALS["BE_USER"]->user["uid"]);
		$tce->process_datamap();
		if($tce->errorLog){
			var_dump($data);
			throw new Exception(print_r($tce->errorLog, true));
		}
	}
}