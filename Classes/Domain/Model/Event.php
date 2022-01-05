<?php
namespace GuteBotschafter\GbEvents\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011-2015 Morton Jonuschat <m.jonuschat@gute-botschafter.de>, Gute Botschafter GmbH
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

use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * A single event
 */
class Event extends AbstractEntity
{
    /**
     * The title of the event
     *
     * @var string
     */
    protected $title;

    /**
     * A short teaser text
     *
     * @var string
     */
    protected $teaser;

    /**
     * A detailed description of the event
     *
     * @var string
     */
    protected $description;

    /**
     * The location of the event
     *
     * @var string
     */
    protected $location;

    /**
     * The date when the event happens
     *
     * @var \DateTime
     */
    protected $eventDate;

    /**
     * The time the event happens
     *
     * @var string
     */
    protected $eventTime;

    /**
     * images
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $images;

    /**
     * The downloads for this event
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $downloads;

    /**
     * The weeks of the month the event should occur at
     *
     * @var integer
     */
    protected $recurringWeeks;

    /**
     * The days of the week the event should occur at
     *
     * @var integer
     */
    protected $recurringDays;

    /**
     * The date until which a recurring event should repeat
     *
     * @var \DateTime
     */
    protected $recurringStop;

    /**
     * The date when the event ends
     *
     * @var \DateTime
     */
    protected $eventStopDate;

    /**
     * Exclude national holidays from the recurring events list
     *
     * @var boolean
     */
    protected $recurringExcludeHolidays;

    /**
     * Dates on which recurring events do not occur
     *
     * @var string
     */
    protected $recurringExcludeDates;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\Category>
     */
    protected $categories;

    /**
     * Setup the excluded dates
     *
     * @return void
     */
    // public function initializeExcludedDates()
    // {
    //     if (is_array($this->excludedDates)) {
    //         return;
    //     }
    //     $this->excludedDates = [];
    //
    //     // // Global excludes
    //     // if (intval($this->settings['forceExcludeHolidays']) !== 0
    //     //     || $this->getRecurringExcludeHolidays() === true
    //     // ) {
    //     //     if (is_array($this->settings['holidays'])
    //     //         && count($this->settings['holidays']) !== 0
    //     //     ) {
    //     //         foreach ($this->settings['holidays'] as $holiday) {
    //     //             try {
    //     //                 $date = $this->expandExcludeDate($holiday);
    //     //                 $this->excludedDates[$date->format('Y')][$date->format('m-d')] = 1;
    //     //             } catch (\Exception $e) {
    //     //                 continue;
    //     //             }
    //     //         }
    //     //     }
    //     // }
    //     // // Per event excludes
    //     // foreach ($this->getRecurringExcludeDatesArray() as $excludedDate) {
    //     //     if (trim($excludedDate) === '') {
    //     //         continue;
    //     //     }
    //     //     try {
    //     //         $date = $this->expandExcludeDate($excludedDate);
    //     //         $this->excludedDates[$date->format('Y')][$date->format('m-d')] = 1;
    //     //     } catch (\Exception $e) {
    //     //         continue;
    //     //     }
    //     // }
    // }

    /**
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $teaser
     * @return void
     */
    public function setTeaser($teaser)
    {
        $this->teaser = $teaser;
    }

    /**
     * @return string
     */
    public function getTeaser()
    {
        return $this->teaser;
    }

    /**
     * @param string $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get plain description with no HTML
     *
     * @return string
     */
    public function getPlainDescription()
    {
        return strip_tags($this->getDescription());
    }

    /**
     * @param string $location
     * @return void
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param \DateTime $eventDate
     * @return void
     */
    public function setEventDate(\DateTime $eventDate)
    {
        $this->eventDate = $eventDate;
    }

    /**
     * This only returns the initial event date
     *
     * @return \DateTime
     */
    public function getEventDate()
    {
        return $this->eventDate->modify('midnight');
    }

    /**
     * @param string $eventTime
     * @return void
     */
    public function setEventTime($eventTime)
    {
        $this->eventTime = $eventTime;
    }

    /**
     * @return string
     */
    public function getEventTime()
    {
        return $this->eventTime;
    }

    /**
     * @param ObjectStorage $images
     * @return void
     */
    public function setImages($images)
    {
        $this->images = $images;
    }

    /**
     * @return ObjectStorage
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param ObjectStorage $downloads
     * @return void
     */
    public function setDownloads($downloads)
    {
        $this->downloads = $downloads;
    }

    /**
     * @return ObjectStorage
     */
    public function getDownloads()
    {
        return $this->downloads;
    }

    /**
     * @param integer $recurringWeeks
     * @return void
     */
    public function setRecurringWeeks($recurringWeeks)
    {
        $this->recurringWeeks = $recurringWeeks;
    }

    /**
     * @return integer
     */
    public function getRecurringWeeks()
    {
        return $this->recurringWeeks;
    }

    /**
     * @param integer $recurringDays
     * @return void
     */
    public function setRecurringDays($recurringDays)
    {
        $this->recurringDays = $recurringDays;
    }

    /**
     * @return integer
     */
    public function getRecurringDays()
    {
        return $this->recurringDays;
    }

    /**
     * @param \DateTime $recurringStop
     * @return void
     */
    public function setRecurringStop($recurringStop)
    {
        $this->recurringStop = $recurringStop;
    }

    /**
     * @return \DateTime
     */
    public function getRecurringStop()
    {
        return $this->recurringStop;
    }

    /**
     * @param boolean $recurringExcludeHolidays
     * @return void
     */
    public function setRecurringExcludeHolidays($recurringExcludeHolidays)
    {
        $this->recurringExcludeHolidays = $recurringExcludeHolidays;
    }

    /**
     * @return boolean
     */
    public function getRecurringExcludeHolidays()
    {
        return $this->recurringExcludeHolidays;
    }

    /**
     * Set the event stop date
     *
     * @param \DateTime $eventStopDate
     * @return void
     */
    public function setEventStopDate($eventStopDate)
    {
        $this->eventStopDate = $eventStopDate;
    }

    /**
     * Get the event stop date
     *
     * @return \DateTime
     */
    public function getEventStopDate()
    {
        return ($this->eventStopDate == '') ? $this->eventDate : $this->eventStopDate;
    }

    /**
     * Is it a one-day event?
     *
     * @return boolean
     */
    public function getIsOneDayEvent()
    {
        return $this->getEventStopDate() == $this->getEventDate();
    }




    /**
     * Gets the Dates on which recurring events do not occur.
     *
     * @return string
     */
    public function getRecurringExcludeDates()
    {
        return $this->recurringExcludeDates;
    }

    /**
     * Sets the Dates on which recurring events do not occur.
     *
     * @param string $recurringExcludeDates the recurring exclude dates
     * @return void
     */
    public function setRecurringExcludeDates($recurringExcludeDates)
    {
        $this->recurringExcludeDates = $recurringExcludeDates;
    }

    /**
     * Returns true if this event is recurring in any fashion.
     *
     * @return bool
     */
    public function isRecurringEvent()
    {
        return $this->recurringDays || $this->recurringWeeks;
    }

    /**
     * Return a unique identifier
     *
     * @return string
     */
    public function getUniqueIdentifier()
    {
        return md5($this->uid . ':' . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']);
    }

    /**
     * Return the duration in seconds for an event
     *
     * @return integer $duration
     */
    public function getDuration()
    {
        return $this->getEventStopDate()->getTimestamp() - $this->getEventDate()->getTimestamp();
    }

    /**
     * @param FileReference $image
     */
    public function addImage(FileReference $image)
    {
        $this->images->attach($image);
    }

    /**
     * @param FileReference $image
     */
    public function removeImage(FileReference $image)
    {
        $this->images->detach($image);
    }

    /**
     * @param FileReference $download
     */
    public function addDownload(FileReference $download)
    {
        $this->images->attach($download);
    }

    /**
     * @param FileReference $download
     */
    public function removeDownload(FileReference $download)
    {
        $this->images->detach($download);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }
}
