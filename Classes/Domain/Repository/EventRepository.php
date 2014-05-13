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


/**
 * Repository for GuteBotschafter\GbEvents\Domain\Model\Event
 */
class EventRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {
  /**
   * Find all events between $startDate and $stopDate
   * @param  \DateTime $startDate
   * @param  \DateTime $stopDate
   * @return \array $events
   */
  public function findAllBetween(\DateTime $startDate, \DateTime $stopDate) {
    $query = $this->createQuery();
    $query->setOrderings(array('event_date' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING));
    $conditions = $query->logicalOr(
      // Einzelne Veranstaltung im gesuchten Zeitfenster
      $query->logicalAnd(
        $query->greaterThanOrEqual('event_date', $startDate),
        $query->lessThanOrEqual('event_date', $stopDate)
      )
    );
    $this->applyRecurringConditions($query, $conditions, $startDate, $stopDate);
    return $this->resolveRecurringEvents($query->execute(), $grouped = TRUE, $startDate, $stopDate);
  }

  /**
   * Find all events (limited to a amount of years)
   * @param  \integer $years
   * @return \array $events
   */
  public function findAll($years = NULL) {
    if(intval($years) === 0) {
      $years = 1;
    }

    $startDate = new \DateTime('midnight');
    $stopDate = new \DateTime(sprintf('midnight + %d years', intval($years)));

    $query = $this->createQuery();
    $query->setOrderings(array('event_date' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING));
    $conditions = $query->logicalAnd(
      $query->greaterThanOrEqual('event_date', $startDate),
      $query->lessThanOrEqual('event_date', $stopDate)
    );
    $this->applyRecurringConditions($query, $conditions, $startDate, $stopDate);
    return $this->resolveRecurringEvents($query->execute(), $grouped = FALSE, $startDate, $stopDate);
  }

  /**
   * Find upcoming events (limited to a count of n)
   * @param  \integer $limit
   * @return \array
   */
  public function findUpcoming($limit = 3) {
    if(intval($limit) === 0) {
      $limit = 3;
    }

    $startDate = new \DateTime('midnight');
    $stopDate = new \DateTime('midnight + 5 years');

    $query = $this->createQuery();
    $query->setOrderings(array('event_date' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING));
    $conditions = $query->greaterThanOrEqual('event_date', $startDate);
    $this->applyRecurringConditions($query, $conditions, $startDate, $stopDate);
    return $this->resolveRecurringEvents($query->execute(), $grouped = FALSE, $startDate, $stopDate, $limit);
  }

  /**
   * Add conditions to retrieve recurring dates from the database
   *
   * @param \TYPO3\CMS\Extbase\Persistence\QueryInterface $query The query object
   * @param \TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface $conditions The query conditions
   * @param \DateTime $startDate
   * @param \DateTime $stopDate
   * @return \void
   */
  protected function applyRecurringConditions(\TYPO3\CMS\Extbase\Persistence\QueryInterface &$query, \TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface $conditions, \DateTime $startDate, \DateTime $stopDate) {
    $query->matching(
      $query->logicalOr(
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
      )
    );
  }

  /**
   * Resolve the recurring events into current dates honoring start and stopdates as well as limits
   * on the amount of dates returned
   *
   * @param \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $events
   * @param \bool $grouped
   * @param \DateTime $startDate
   * @param \DateTime $stopDate
   * @param \integer $limit
   * @return \array $days
   */
  protected function resolveRecurringEvents(\TYPO3\CMS\Extbase\Persistence\QueryResultInterface $events, $grouped = FALSE, \DateTime $startDate, \DateTime $stopDate, $limit = NULL) {
    $today = new \DateTime('midnight');
    $days = array();
    foreach($events as $event) {
      foreach($event->getEventDates($startDate, $stopDate) as $eventDate) {
        if(($grouped === FALSE && $eventDate->format('U') < $today->format('U')) || ($grouped === TRUE && $eventDate->format('U') < $startDate->format('U'))) {
          continue;
        }
        $recurringEvent = clone($event);
        $recurringEvent->setEventDate($eventDate);
        if($grouped) {
          $days[$eventDate->format('Y-m-d')]['events'][$event->getUid()] = $recurringEvent;
        } else {
          $days[$eventDate->format('Y-m-d') . '_' . $event->getTitle()] = $recurringEvent;
        }
      }
    }
    ksort($days);

    if(!is_null($limit)) {
      $days = array_slice($days, 0, $limit, TRUE);
    }

    return $days;
  }
}
