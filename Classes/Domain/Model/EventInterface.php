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

/**
 * A single event
 */
interface EventInterface {
  /**
   * @param \string $title
   * @return void
   */
  function setTitle($title);

  /**
   * @return \string
   */
  function getTitle();

  /**
   * @param \string $teaser
   * @return void
   */
  function setTeaser($teaser);

  /**
   * @return \string
   */
  function getTeaser();

  /**
   * @param \string $description
   * @return void
   */
  function setDescription($description);

  /**
   * @return \string
   */
  function getDescription();

  /**
    * Get plain description with no HTML
    *
    * @return \string
    */
  function getPlainDescription();

  /**
   * @param \string $location
   * @return void
   */
  function setLocation($location);

  /**
   * @return \string
   */
  function getLocation();

  /**
   * @param \DateTime $eventDate
   * @return void
   */
  function setEventDate(\DateTime $eventDate);

  /**
   * This only returns the initial event date
   *
   * @return \DateTime
   */
  function getEventDate();

  /**
   * This returns the initial event dates including
   * all recurring events up to and including the
   * stopDate, taking the defined end of recurrance
   * into account
   *
   * @param \DateTime $startDate
   * @param \DateTime $stopDate
   */
  function getEventDates(\DateTime $startDate, \DateTime $stopDate);

  /**
   * @param \string $eventTime
   * @return void
   */
  function setEventTime($eventTime);

  /**
   * @return \string
   */
  function getEventTime();

  /**
   * @param \string $images
   * @return void
   */
  function setImages($images);

  /**
   * @return \array
   */
  function getImages();

  /**
   * @param \string $downloads
   * @return void
   */
  function setDownloads($downloads);

  /**
   * @return \array
   */
  function getDownloads();

  /**
   * @param \integer $recurringWeeks
   * @return void
   */
  function setRecurringWeeks($recurringWeeks);

  /**
   * @return \integer
   */
  function getRecurringWeeks();

  /**
   * @param \integer $recurringDays
   * @return void
   */
  function setRecurringDays($recurringDays);

  /**
   * @return \integer
   */
  function getRecurringDays();

  /**
   * @param \DateTime $recurringStop
   * @return void
   */
  function setRecurringStop($recurringStop);

  /**
   * @return \DateTime
   */
  function getRecurringStop();

  /**
   * @param \boolean $recurringExcludeHolidays
   * @return void
   */
  function setRecurringExcludeHolidays($recurringExcludeHolidays);

  /**
   * @return \boolean
   */
  function getRecurringExcludeHolidays();

  /**
   * Set the event stop date
   *
   * @param \DateTime $eventStopDate
   * @return void
   */
  function setEventStopDate($eventStopDate);

  /**
   * Get the event stop date
   *
   * @return \DateTime
   */
  function getEventStopDate();

  /**
   * Is it a one-day event?
   *
   * @return \bool
   */
   function getIsOneDayEvent();
}
