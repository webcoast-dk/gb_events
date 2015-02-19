<?php
namespace GuteBotschafter\GbEvents\Domain\Repository;

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

use TYPO3\CMS\Core\Utility\GeneralUtility as GeneralUtility;

/**
 * Repository for GuteBotschafter\GbEvents\Domain\Model\Event
 */
class EventRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {
  /**
   * Find all events between $startDate and $stopDate
   *
   * @param  \DateTime  $startDate
   * @param  \DateTime  $stopDate
   * @param  boolean    $showActive
   * @param  string     $categories
   * @return array      $events
   */
  public function findAllBetween(\DateTime $startDate, \DateTime $stopDate, $showActive = FALSE, $categories = NULL) {
    $query = $this->queryAllBetween($startDate, $stopDate, $showActive, $categories);
    return $this->resolveRecurringEvents($query->execute(), $grouped = TRUE, $startDate, $stopDate, $showActive);
  }

  /**
   * Find all events (limited to a amount of years)
   * @param  \integer   $years
   * @param  boolean    $showActive
   * @param  string     $categories
   * @return array      $events
   */
  public function findAll($years = NULL, $showActive = FALSE, $categories = NULL) {
    if(intval($years) === 0) {
      $years = 1;
    }

    $startDate = new \DateTime('midnight');
    $stopDate = new \DateTime(sprintf('midnight + %d years', intval($years)));

    $query = $this->queryAllBetween($startDate, $stopDate, $showActive, $categories);
    return $this->resolveRecurringEvents($query->execute(), $grouped = FALSE, $startDate, $stopDate, $showActive);
  }

  /**
   * Find upcoming events (limited to a count of n)
   * @param  \integer   $limit
   * @param  boolean    $showActive
   * @param  string     $categories
   * @return array
   */
  public function findUpcoming($limit = 3, $showActive = FALSE, $categories = NULL) {
    if(intval($limit) === 0) {
      $limit = 3;
    }

    $startDate = new \DateTime('midnight');
    $stopDate = new \DateTime('midnight + 5 years');

    $query = $this->createQuery();
    $query->setOrderings(array('event_date' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING));
    $conditions = $query->greaterThanOrEqual('event_date', $startDate);
    $this->applyRecurringConditions($query, $conditions, $startDate, $stopDate, $categories);
    return $this->resolveRecurringEvents($query->execute(), $grouped = FALSE, $startDate, $stopDate, $showActive, $limit);
  }

  /**
   * Find past events (limited to a count of n)
   * @param  \integer   $limit
   * @param  string     $categories
   * @return array
   */
  public function findBygone($limit = 3, $categories = NULL) {
    if(intval($limit) === 0) {
      $limit = 3;
    }

    $startDate = new \DateTime('midnight - 10 years');
    $stopDate = new \DateTime('midnight - 1 second');
    $cutOffDate = new \DateTime($limit . ' years ago midnight');

    $query = $this->createQuery();
    $query->setOrderings(array('event_date' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING));
    $conditions = $query->greaterThanOrEqual('event_date', $startDate);
    $this->applyRecurringConditions($query, $conditions, $startDate, $stopDate, $categories);
    $events = array_filter(
      $this->resolveRecurringEvents($query->execute(), $grouped = FALSE, $startDate, $stopDate, TRUE),
      function($event) use (&$cutOffDate, &$stopDate) { return $event->getEventDate() >= $cutOffDate && $event->getEventDate() <= $stopDate; }
    );
    usort($events, function($a, $b) { return strcmp($a->getEventDate()->getTimestamp(), $b->getEventDate()->getTimestamp()); });
    return array_reverse($events);
  }

  /**
   * Add conditions to retrieve recurring dates from the database
   *
   * @param \TYPO3\CMS\Extbase\Persistence\QueryInterface $query The query object
   * @param \TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface $conditions The query conditions
   * @param \DateTime $startDate
   * @param \DateTime $stopDate
   * @param array $categories
   * @return \void
   */
   protected function applyRecurringConditions(\TYPO3\CMS\Extbase\Persistence\QueryInterface &$query, \TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface $conditions, \DateTime $startDate, \DateTime $stopDate, $categories = NULL) {
    $conditions = $query->logicalOr(
      $conditions,
      // Wiederkehrende Veranstaltung
      $query->logicalAnd(
        // Beginnt vor dem Ende des gesuchten Zeitraums
        $query->lessThanOrEqual('event_date', $stopDate),
        // Mindestens ein Wiederholungskriterium gesetzt
        $query->logicalOr(
          $query->greaterThan('recurringDays', 0),
          $query->greaterThan('recurringWeeks', 0)
        ),
        // Kein Enddatum oder Enddatum im/nach dem gesuchten Startdatum
        $query->logicalOr(
          $query->equals('recurringStop', 0),
          $query->greaterThanOrEqual('recurringStop', $startDate)
        )
      )
    );
    $this->applyCategoryFilters($query, $conditions, $categories);
    $query->matching($conditions);
  }

  /**
   * Add conditions to filter the selected records by category
   *
   * @param  \TYPO3\CMS\Extbase\Persistence\QueryInterface                  $query      The query object
   * @param  \TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface $conditions The query conditions
   * @param  string                                                         $categories
   * @return \void
   */
  protected function applyCategoryFilters(\TYPO3\CMS\Extbase\Persistence\QueryInterface $query, \TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface &$conditions, $categories) {
    $categories = GeneralUtility::intExplode(',', $categories, TRUE);
    if(is_array($categories) && !empty($categories)) {
      $categoryConditions = array_map(
        function($category) use ($query) { return $query->contains('categories', $category); },
        $categories
      );
      $conditions = $query->logicalAnd(
        $conditions,
        $query->logicalOr($categoryConditions)
      );
    }
  }

  /**
   * Resolve the recurring events into current dates honoring start and stopdates as well as limits
   * on the amount of dates returned
   *
   * @param  \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $events
   * @param  \bool $grouped
   * @param  \DateTime $startDate
   * @param  \DateTime $stopDate
   * @param  boolean $checkDuration
   * @param  \integer $limit
   * @return array $days
   */
  protected function resolveRecurringEvents(\TYPO3\CMS\Extbase\Persistence\QueryResultInterface $events, $grouped = FALSE, \DateTime $startDate, \DateTime $stopDate, $checkDuration = FALSE, $limit = NULL) {
    $today = new \DateTime('midnight');
    $days = array();
    foreach($events as $event) {
      foreach($event->getEventDates($startDate, $stopDate) as $eventDate) {
        if($grouped === FALSE) {
          if($checkDuration === FALSE && !$this->isVisibleEvent($eventDate)) {
            continue;
          }
          if($checkDuration === TRUE && !$this->isVisibleEvent($eventDate, $event->getDuration())) {
            continue;
          }
        } else {
          if(!$this->isVisibleEvent($eventDate, 0, $startDate)) {
            continue;
          }
        }
        $recurringEvent = clone($event);
        $recurringEvent->setEventDate($eventDate);
        if($grouped) {
          $days[$eventDate->format('Y-m-d')]['events'][$event->getUid()] = $recurringEvent;
        } else {
          $days[$eventDate->format('Y-m-d') . '_' . $event->getUniqueIdentifier()] = $recurringEvent;
        }
      }
    }
    ksort($days);

    if(!is_null($limit)) {
      $days = array_slice($days, 0, $limit, TRUE);
    }

    return $days;
  }

  /**
   * Check if the event is active at the given point in time
   *
   * @param \DateTime $eventDate
   * @param integer   $duration
   * @param \DateTime $currentDate
   * @return boolean
   */
  protected function isVisibleEvent(\DateTime $eventDate, $duration = 0, \DateTime $currentDate = NULL) {
    if(is_null($currentDate)) {
      $currentDate = new \DateTime('midnight');
    }
    return ($eventDate->getTimestamp() + $duration) >= $currentDate->getTimestamp();
  }

  /**
   * Returns query constraints for simple events.
   *
   * @param   \TYPO3\CMS\Extbase\Persistence\QueryInterface                   $query
   * @param   \DateTime                                                       $startDate
   * @param   \DateTime                                                       $stopDate
   * @param   boolean                                                         $showActive
   * @return  \TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface  $conditions
   */
  protected function getBaseConditions(\TYPO3\CMS\Extbase\Persistence\QueryInterface &$query, \DateTime $startDate, \DateTime $stopDate, $showActive = FALSE) {
    $conditions = $query->logicalAnd(
      $query->greaterThanOrEqual('event_date', $startDate),
      $query->lessThanOrEqual('event_date', $stopDate)
    );
    if($showActive == TRUE) {
      $conditions = $query->logicalOr(
        $conditions,
        $query->logicalAnd(
          $query->lessThanOrEqual('event_date', $startDate),
          $query->greaterThanOrEqual('event_stop_date', $startDate)
        )
      );
    }
    return $conditions;
  }

  /**
   * Find all events between $startDate and $stopDate
   *
   * @param  \DateTime  $startDate
   * @param  \DateTime  $stopDate
   * @param  boolean    $showActive
   * @param  string     $categories
   * @return array      $events
   */
  protected function queryAllBetween(\DateTime $startDate, \DateTime $stopDate, $showActive = FALSE, $categories = NULL) {
    $query = $this->createQuery();
    $query->setOrderings(array('event_date' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING));
    $conditions = $this->getBaseConditions($query, $startDate, $stopDate, $showActive);
    $this->applyRecurringConditions($query, $conditions, $startDate, $stopDate, $categories);
    return $query;
  }
}
