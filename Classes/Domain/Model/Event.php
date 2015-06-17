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
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Utility\ArrayUtility;

/**
 * A single event
 */
class Event extends AbstractEntity implements EventInterface {
  /**
   * Configuration Manager
   * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
   * @inject
   */
  protected $configurationManager;

  /**
   * Extension settings
   * @var array
   */
  protected $settings;

  /**
   * Extension settings
   * @var array
   */
  protected $excludedDates;

  /**
   * The title of the event
   *
   * @var string
   * @validate NotEmpty
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
   * @validate NotEmpty
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
   * @validate NotEmpty
   */
  protected $eventDate;

  /**
   * The time the event happens
   *
   * @var string
   */
  protected $eventTime;

  /**
   * The images for this event
   *
   * @var string
   */
  protected $images;

  /**
   * The downloads for this event
   *
   * @var string
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
   * Setup for the Event object
   *
   * @return void
   */
  public function initializeSettings() {
    if(is_null($this->settings)) {
      $this->settings = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS);
    }
  }

  /**
   * Setup the excluded dates
   *
   * @return void
   */
  public function initializeExcludedDates() {
    $this->initializeSettings();
    if(is_array($this->excludedDates)) {
      return;
    }
    $this->excludedDates = array();

    // Global excludes
    if(intval($this->settings['forceExcludeHolidays']) !== 0 || $this->getRecurringExcludeHolidays() === TRUE) {
      if(is_array($this->settings['holidays']) && count($this->settings['holidays']) !== 0) {
        foreach($this->settings['holidays'] as $holiday) {
          try {
            $date = $this->expandExcludeDate($holiday);
            $this->excludedDates[$date->format('Y')][$date->format('m-d')] = 1;
          } catch(\Exception $e) {
            continue;
          }
        }
      }
    }
    // Per event excludes
    foreach($this->getRecurringExcludeDatesArray() as $excludedDate) {
      if(trim($excludedDate) === '') {
        continue;
      }
      try {
        $date = $this->expandExcludeDate($excludedDate);
        $this->excludedDates[$date->format('Y')][$date->format('m-d')] = 1;
      } catch(\Exception $e) {
        continue;
      }
    }
  }

  /**
   * @param string $title
   * @return void
   */
  public function setTitle($title) {
    $this->title = $title;
  }

  /**
   * @return string
   */
  public function getTitle() {
    return $this->title;
  }

  /**
   * @param string $teaser
   * @return void
   */
  public function setTeaser($teaser) {
    $this->teaser = $teaser;
  }

  /**
   * @return string
   */
  public function getTeaser() {
    return $this->teaser;
  }

  /**
   * @param string $description
   * @return void
   */
  public function setDescription($description) {
    $this->description = $description;
  }

  /**
   * @return string
   */
  public function getDescription() {
    return $this->description;
  }

  /**
    * Get plain description with no HTML
    *
    * @return string
    */
  public function getPlainDescription() {
    return strip_tags($this->getDescription());
  }

  /**
   * @param string $location
   * @return void
   */
  public function setLocation($location) {
    $this->location = $location;
  }

  /**
   * @return string
   */
  public function getLocation() {
    return $this->location;
  }

  /**
   * @param \DateTime $eventDate
   * @return void
   */
  public function setEventDate(\DateTime $eventDate) {
    $this->eventDate = $eventDate;
  }

  /**
   * This only returns the initial event date
   *
   * @return \DateTime
   */
  public function getEventDate() {
    return $this->eventDate->modify('midnight');
  }

  /**
   * This returns the initial event dates including
   * all recurring events up to and including the
   * stopDate, taking the defined end of recurrance
   * into account
   *
   * @param \DateTime $startDate
   * @param \DateTime $stopDate
   * @param bool      $expandedList
   * @return array
   */
  public function getEventDates(\DateTime $startDate, \DateTime $stopDate, $expandedList = FALSE) {
    $this->initializeSettings();
    $oneDay = new \DateInterval('P1D');
    $oneMonth = new \DateInterval('P1M');

    $startMonth = clone($startDate);
    $startMonth->modify('first day of this month');
    $stopMonth = clone($stopDate);
    $stopMonth->modify('last day of this month');
    $recurringMonths = array();

    while($startMonth <= $stopMonth) {
      $recurringMonths[] = clone($startMonth);
      $startMonth->add($oneMonth);
    }

    $recurringWeeks = $this->getRecurringWeeksAsText();
    $recurringDays = $this->getRecurringDaysAsText();
    $eventDates = array();
    foreach($recurringMonths as $workDate) {
      /** @var \DateTime $workDate */
      $workingMonth = $workDate->format('n');

      // Weeks have been selected, check every nth week / day combination
      if(count($recurringWeeks) !== 0) {
        foreach($this->getRecurringWeeksAsText() as $week) {
          foreach($this->getRecurringDaysAsText() as $day) {
            $workDate->modify(sprintf('%s %s of this month', $week, $day));
            if($workingMonth === $workDate->format('n') && $workDate >= $this->getEventDate() && (is_null($this->getRecurringStop()) || $workDate <= $this->getRecurringStop()) && $workDate >= $startDate && $workDate <= $stopDate) {
              if($this->isExcludedDate($workDate)) {
                continue;
              }
              $eventDates[$workDate->format('Y-m-d')] = clone($workDate);
              if(!$this->settings['startDateOnly']) {
                $re_StartDate = clone($workDate);
                $difference = $this->getEventDate()->diff($re_StartDate);
                $re_StopDate = clone($this->getEventStopDate());
                $re_StopDate->add($difference);
                while($re_StartDate <= $re_StopDate) {
                  $eventDates[$re_StartDate->format('Y-m-d')] = clone($re_StartDate);
                  $re_StartDate->modify('+1 day');
                }
              }
            }
          }
        }
      } else {
        // Check the weekdays only, ignoring the weeks of the month
        $stopDay = clone($workDate);
        $stopDay->modify('last day of this month');
        while($workDate <= $stopDay) {
          $addCurrentDay = FALSE;
          switch($workDate->format('w')) {
          case 0:
          case 7:
            $addCurrentDay = in_array('Sunday', $recurringDays);
            break;
          case 1:
            $addCurrentDay = in_array('Monday', $recurringDays);
            break;
          case 2:
            $addCurrentDay = in_array('Tuesday', $recurringDays);
            break;
          case 3:
            $addCurrentDay = in_array('Wednesday', $recurringDays);
            break;
          case 4:
            $addCurrentDay = in_array('Thursday', $recurringDays);
            break;
          case 5:
            $addCurrentDay = in_array('Friday', $recurringDays);
            break;
          case 6:
            $addCurrentDay = in_array('Saturday', $recurringDays);
            break;
          }
          if($addCurrentDay && !$this->isExcludedDate($workDate)) {
            if($workDate >= $this->getEventDate() && (is_null($this->getRecurringStop()) || $workDate <= $this->getRecurringStop()) && $workDate >= $startDate && $workDate <= $stopDate) {
              $eventDates[$workDate->format('Y-m-d')] = clone($workDate);
              if(!$this->settings['startDateOnly'] || $expandedList) {
                $re_StartDate = clone($workDate);
                $difference = $this->getEventDate()->diff($re_StartDate);
                $re_StopDate = clone($this->getEventStopDate());
                $re_StopDate->add($difference);
                while($re_StartDate <= $re_StopDate) {
                  $eventDates[$re_StartDate->format('Y-m-d')] = clone($re_StartDate);
                  $re_StartDate->modify('+1 day');
                }
              }
            }
          }
          $workDate->add($oneDay);
        }
      }
    }
    $myStartDate = clone($this->getEventDate());
    $myStopDate = clone($this->getEventStopDate());
    if(!$this->settings['startDateOnly'] || $expandedList) {
      while($myStartDate <= $myStopDate) {
        if(!$this->isExcludedDate($myStartDate)) {
          $eventDates[$myStartDate->format('Y-m-d')] = clone($myStartDate);
        }
        $myStartDate->modify('+1 day');
      }
    } else {
      $eventDates[$myStartDate->format('Y-m-d')] = clone($myStartDate);
    }

    $eventDates[$this->getEventDate()->format('Y-m-d')] = $this->getEventDate();
    ksort($eventDates);
    return $eventDates;
  }

  /**
   * @param string $eventTime
   * @return void
   */
  public function setEventTime($eventTime) {
    $this->eventTime = $eventTime;
  }

  /**
   * @return string
   */
  public function getEventTime() {
    return $this->eventTime;
  }

  /**
   * @param string $images
   * @return void
   */
  public function setImages($images) {
    $this->images = $images;
  }

  /**
   * @return array
   */
  public function getImages() {
    $mapFunc = create_function('$i', 'return "uploads/tx_gbevents/" . $i;');
    return array_map($mapFunc, ArrayUtility::trimExplode(',', $this->images, TRUE));
  }

  /**
   * @param string $downloads
   * @return void
   */
  public function setDownloads($downloads) {
    $this->downloads = $downloads;
  }

  /**
   * @return array
   */
  public function getDownloads() {
    $mapFunc = create_function('$i', 'return array("file" => "uploads/tx_gbevents/" . $i, "name" => basename($i));');
    return array_map($mapFunc, ArrayUtility::trimExplode(',', $this->downloads, TRUE));
  }

  /**
   * @param integer $recurringWeeks
   * @return void
   */
  public function setRecurringWeeks($recurringWeeks) {
    $this->recurringWeeks = $recurringWeeks;
  }

  /**
   * @return integer
   */
  public function getRecurringWeeks() {
    return $this->recurringWeeks;
  }

  /**
   * @return array
   */
  protected function getRecurringWeeksAsText() {
    $weeks = array();
    if($this->getRecurringWeeks() & 1) {
      $weeks[] = 'first';
    }
    if($this->getRecurringWeeks() & 2) {
      $weeks[] = 'second';
    }
    if($this->getRecurringWeeks() & 4) {
      $weeks[] = 'third';
    }
    if($this->getRecurringWeeks() & 8) {
      $weeks[] = 'fourth';
    }
    if($this->getRecurringWeeks() & 16) {
      $weeks[] = 'fifth';
    }
    if($this->getRecurringWeeks() & 32) {
      $weeks[] = 'last';
    }
    return $weeks;
  }

  /**
   * @param integer $recurringDays
   * @return void
   */
  public function setRecurringDays($recurringDays) {
    $this->recurringDays = $recurringDays;
  }

  /**
   * @return integer
   */
  public function getRecurringDays() {
    return $this->recurringDays;
  }

  /**
   * @param \DateTime $recurringStop
   * @return void
   */
  public function setRecurringStop($recurringStop) {
    $this->recurringStop = $recurringStop;
  }

  /**
   * @return \DateTime
   */
  public function getRecurringStop() {
    return $this->recurringStop;
  }

  /**
   * @param boolean $recurringExcludeHolidays
   * @return void
   */
  public function setRecurringExcludeHolidays($recurringExcludeHolidays) {
    $this->recurringExcludeHolidays = $recurringExcludeHolidays;
  }

  /**
   * @return boolean
   */
  public function getRecurringExcludeHolidays() {
    return $this->recurringExcludeHolidays;
  }

  /**
   * Set the event stop date
   *
   * @param \DateTime $eventStopDate
   * @return void
   */
  public function setEventStopDate($eventStopDate) {
    $this->eventStopDate = $eventStopDate;
  }

  /**
   * Get the event stop date
   *
   * @return \DateTime
   */
  public function getEventStopDate() {
    return ($this->eventStopDate == '') ? $this->eventDate : $this->eventStopDate;
  }

  /**
   * Is it a one-day event?
   *
   * @return \bool
   */
   public function getIsOneDayEvent() {
     return $this->getEventStopDate() == $this->getEventDate();
  }

  /**
   * Return a suggested filename for sending the iCalendar file to the client
   *
   * @return string $filename;
   */
  public function iCalendarFilename() {
    return sprintf('%s - %s.ics', $this->getTitle(), $this->getEventDate()->format('Y-m-d'));
  }

  /**
   * Return an iCalendar file as string representation suitable for sending to the client
   *
   * @return string $iCalendarData
   */
  public function iCalendarData() {
    $now = new \DateTime();
    $startDate = clone($this->getEventDate());
    $startDate->add($this->getEventTimeAsDateInterval());
    $stopDate = clone($this->getEventStopDate());
    $stopDate->add($this->getEventTimeAsDateInterval())->add(new \DateInterval('PT1H'));

    $iCalData = array();

    $iCalData[] = 'BEGIN:VEVENT';
    $iCalData[] = 'UID:' . $this->getUniqueIdentifier();
    $iCalData[] = 'LOCATION:' . $this->getLocation();
    $iCalData[] = 'SUMMARY:' . $this->getTitle();
    $iCalData[] = 'DESCRIPTION:' . html_entity_decode(strip_tags($this->getDescription()), ENT_COMPAT | ENT_HTML401, 'UTF-8');
    $iCalData[] = 'CLASS:PUBLIC';
    if($this->getIsOneDayEvent()) {
      $iCalData[] = 'DTSTART;VALUE=DATE:' . $startDate->format('Ymd');
      $iCalData[] = 'DTEND;VALUE=DATE:' . $stopDate->format('Ymd');
    } else {
      $iCalData[] = 'DTSTART:' . $startDate->format('Ymd\THis');
      $iCalData[] = 'DTEND:' . $stopDate->format('Ymd\THis');
    }
    $iCalData[] = 'DTSTAMP:' . $now->format('Ymd\THis');
    if($this->isRecurringEvent()) {
      $iCalData[] = 'RRULE:' . $this->buildRecurrenceRule();
    }
    $iCalData[] = 'END:VEVENT';

    return join("\n", $iCalData);
  }

  /**
   * @return array
   */
  protected function getRecurringDaysAsText() {
    $days = array();
    if($this->getRecurringDays() === 0 && $this->getRecurringWeeks() !== 0) {
      switch($this->getEventDate()->format('w')) {
      case 0:
      case 7:
        $days[] = 'Sunday';
        break;
      case 1:
        $days[] = 'Monday';
        break;
      case 2:
        $days[] = 'Tuesday';
        break;
      case 3:
        $days[] = 'Wednesday';
        break;
      case 4:
        $days[] = 'Thursday';
        break;
      case 5:
        $days[] = 'Friday';
        break;
      case 6:
        $days[] = 'Saturday';
        break;
      }
    } else {
      if($this->getRecurringDays() & 1) {
        $days[] = 'Monday';
      }
      if($this->getRecurringDays() & 2) {
        $days[] = 'Tuesday';
      }
      if($this->getRecurringDays() & 4) {
        $days[] = 'Wednesday';
      }
      if($this->getRecurringDays() & 8) {
        $days[] = 'Thursday';
      }
      if($this->getRecurringDays() & 16) {
        $days[] = 'Friday';
      }
      if($this->getRecurringDays() & 32) {
        $days[] = 'Saturday';
      }
      if($this->getRecurringDays() & 64) {
        $days[] = 'Sunday';
      }
    }
    return $days;
  }

  /**
   * Tries an intelligent guess as to the start time of an event
   *
   * @return \DateInterval
   */
  protected function getEventTimeAsDateInterval() {
    $hours = $minutes = 0;
    $matches = array();
    if(preg_match('#(\d{1,2}):?(\d{2})#', $this->getEventTime(), $matches)) {
      $hours = $matches[1];
      $minutes = $matches[2];
    }
    return new \DateInterval(sprintf('PT%dH%dM0S', $hours, $minutes));
  }

  /**
   * Builds iCalendar recurrence rule
   *
   * @return string $rRule
   */
  protected function buildRecurrenceRule() {
    $shortDays = array(
      'Monday' => 'MO',
      'Tuesday' => 'TU',
      'Wednesday' => 'WE',
      'Thursday' => 'TH',
      'Friday' => 'FR',
      'Saturday' => 'SA',
      'Sunday' => 'SU'
    );

    $weeks = array();
    if($this->getRecurringWeeks() & 1) {
      $weeks[] = '1';
    }
    if($this->getRecurringWeeks() & 2) {
      $weeks[] = '2';
    }
    if($this->getRecurringWeeks() & 4) {
      $weeks[] = '3';
    }
    if($this->getRecurringWeeks() & 8) {
      $weeks[] = '4';
    }
    if($this->getRecurringWeeks() & 16) {
      $weeks[] = '5';
    }
    if($this->getRecurringWeeks() & 32) {
      $weeks[] = '-1';
    }

    $days = $this->getRecurringDaysAsText();
    foreach($days as $index => $value) {
      $days[$index] = $shortDays[$value];
    }

    if(count($weeks) !== 0) {
      $rRule = 'FREQ=MONTHLY;BYDAY=';
      $byDays = array();
      foreach($weeks as $week) {
        foreach($days as $day) {
          $byDays[] = sprintf('%s%s', $week, $day);
        }
      }
      $rRule .= join(",", $byDays);
    } else {
      $rRule = 'FREQ=WEEKLY;BYDAY=';
      $byDays = array();
      foreach($days as $day) {
        $byDays[] = $day;
      }
      $rRule .= join(',', $byDays);
    }
    return $rRule;
  }

  /**
   * Gets the Dates on which recurring events do not occur.
   *
   * @return string
   */
  public function getRecurringExcludeDates() {
    return $this->recurringExcludeDates;
  }

  /**
   * Gets the Dates on which recurring events do not occur.
   *
   * @return array
   */
  protected function getRecurringExcludeDatesArray() {
    return preg_split("#[\r\n]+|$#", $this->getRecurringExcludeDates());
  }

  /**
   * Sets the Dates on which recurring events do not occur.
   *
   * @param string $recurringExcludeDates the recurring exclude dates
   * @return void
   */
  public function setRecurringExcludeDates($recurringExcludeDates) {
    $this->recurringExcludeDates = $recurringExcludeDates;
  }

  /**
   * Check if the given date is to be excluded from the list of recurring events
   *
   * @param  \DateTime $date
   * @return boolean
   */
  protected function isExcludedDate(\DateTime $date) {
    $this->initializeExcludedDates();
    if(array_key_exists($date->format('Y'), $this->excludedDates) && array_key_exists($date->format('m-d'), $this->excludedDates[$date->format('Y')])) {
      return TRUE;
    }
    if(array_key_exists('0000', $this->excludedDates) && array_key_exists($date->format('m-d'), $this->excludedDates['0000'])) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Returns true if this event is recurring in any fashion.
   *
   * @return bool
   */
  protected function isRecurringEvent() {
    return $this->recurringDays || $this->recurringWeeks;
  }

  /**
   * Expand the given date to include a year (if missing) and convert to a
   * DateTime object
   * @param  string $excludeDate
   * @return \DateTime
   */
  protected function expandExcludeDate($excludeDate) {
    if(preg_match('#^\d{1,2}\.\d{1,2}\.?$#', $excludeDate)) {
      $excludeDate = str_replace('..', '.', sprintf('%s.%s', $excludeDate, '0000'));
    } else if(preg_match('#^\d{1,2}-\d{1,2}$#', $excludeDate)) {
      $excludeDate = sprintf('%s-%s', '0000', $excludeDate);
    }
    return new \DateTime($excludeDate);
  }

  /**
   * Return a unique identifier
   *
   * @return string
   */
  public function getUniqueIdentifier() {
    return md5($this->uid . ':' . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']);
  }
}
