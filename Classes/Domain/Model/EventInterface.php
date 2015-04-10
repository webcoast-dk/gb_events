<?php
namespace GuteBotschafter\GbEvents\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011-2013 Morton Jonuschat <m.jonuschat@gute-botschafter.de>, Gute Botschafter GmbH
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
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * A single event
 */

interface EventInterface {

  /**
   * @param string $title
   * @return void
   */
  public function setTitle($title);

  /**
   * @return string
   */
  public function getTitle();

  /**
   * @param string $teaser
   * @return void
   */
  public function setTeaser($teaser);

  /**
   * @return string
   */
  public function getTeaser();

  /**
   * @param string $description
   * @return void
   */
  public function setDescription($description);

  /**
   * @return string
   */
  public function getDescription();

  /**
   * Get plain description with no HTML
   *
   * @return string
   */
  public function getPlainDescription();

  /**
   * @param string $location
   * @return void
   */
  public function setLocation($location);

  /**
   * @return string
   */
  public function getLocation();

  /**
   * @param \DateTime $eventDate
   * @return void
   */
  public function setEventDate(\DateTime $eventDate);

  /**
   * This only returns the initial event date
   *
   * @return \DateTime
   */
  public function getEventDate();

  /**
   * This returns the initial event dates including
   * all recurring events up to and including the
   * stopDate, taking the defined end of recurrance
   * into account
   *
   * @param \DateTime $startDate
   * @param \DateTime $stopDate
   */
  public function getEventDates(\DateTime $startDate, \DateTime $stopDate);

  /**
   * @param string $eventTime
   * @return void
   */
  public function setEventTime($eventTime);

  /**
   * @return string
   */
  public function getEventTime();

  /**
   * @param string $images
   * @return void
   */
  public function setImages($images);

  /**
   * @return ObjectStorage<FileReference>
   */
  public function getImages();

  /**
   * @param string $downloads
   * @return void
   */
  public function setDownloads($downloads);

  /**
   * @return ObjectStorage<FileReference>
   */
  public function getDownloads();

  /**
   * @param \integer $recurringWeeks
   * @return void
   */
  public function setRecurringWeeks($recurringWeeks);

  /**
   * @return \integer
   */
  public function getRecurringWeeks();

  /**
   * @param \integer $recurringDays
   * @return void
   */
  public function setRecurringDays($recurringDays);

  /**
   * @return \integer
   */
  public function getRecurringDays();

  /**
   * @param \DateTime $recurringStop
   * @return void
   */
  public function setRecurringStop($recurringStop);

  /**
   * @return \DateTime
   */
  public function getRecurringStop();

  /**
   * @param \boolean $recurringExcludeHolidays
   * @return void
   */
  public function setRecurringExcludeHolidays($recurringExcludeHolidays);

  /**
   * @return \boolean
   */
  public function getRecurringExcludeHolidays();

  /**
   * @param string $recurringExcludeDates
   * @return void
   */
  public function setRecurringExcludeDates(string $recurringExcludeDates);

  /**
   * @return string
   */
  public function getRecurringExcludeDates();

  /**
   * Set the event stop date
   *
   * @param \DateTime $eventStopDate
   * @return void
   */
  public function setEventStopDate($eventStopDate);

  /**
   * Get the event stop date
   *
   * @return \DateTime
   */
  public function getEventStopDate();

  /**
   * Is it a one-day event?
   *
   * @return \bool
   */
  public function getIsOneDayEvent();

  /**
   * Returns a unique identifier
   *
   * @return string
   */
  public function getUniqueIdentifier();

  /**
   * Returns the event duration in seconds
   *
   * @return integer
   */
  public function getDuration();
}
