<?php
namespace GuteBotschafter\GbEvents\Domain\Repository;

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

use TYPO3\CMS\Extbase\Persistence\Generic\Query;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Repository for GuteBotschafter\GbEvents\Domain\Model\Event
 */
class EventRepository extends Repository
{
    /**
     * Find all events between $startDate and $stopDate
     *
     * @param \DateTime $startDate
     * @param \DateTime $stopDate
     * @param  boolean $showStartedEvents
     * @return array
     */
    public function findAllBetween(\DateTime $startDate, \DateTime $stopDate, $showStartedEvents = false)
    {
        /** @var Query $query */
        $query = $this->queryAllBetween($startDate, $stopDate, $showStartedEvents);

        return $this->resolveRecurringEvents(
            $query->execute(),
            $grouped = true,
            $startDate,
            $stopDate,
            $showStartedEvents
        );
    }

    /**
     * Find all events (limited to a amount of years)
     *
     * @param int $years
     * @param boolean $showStartedEvents
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findAll($years = null, $showStartedEvents = false)
    {
        if (intval($years) === 0) {
            $years = 1;
        }

        $startDate = new \DateTime('midnight');
        $stopDate = new \DateTime(sprintf('midnight + %d years', intval($years)));

        /** @var Query $query */
        $query = $this->queryAllBetween($startDate, $stopDate, $showStartedEvents);

        return $this->resolveRecurringEvents(
            $query->execute(),
            $grouped = false,
            $startDate,
            $stopDate,
            $showStartedEvents
        );
    }

    /**
     * Find upcoming events (limited to a count of n)
     *
     * @param int $limit
     * @param boolean $showStartedEvents
     * @return array
     */
    public function findUpcoming($limit = 3, $showStartedEvents = false)
    {
        if (intval($limit) === 0) {
            $limit = 3;
        }

        $startDate = new \DateTime('midnight');
        $stopDate = new \DateTime('midnight + 5 years');

        /** @var Query $query */
        $query = $this->queryAllBetween($startDate, $stopDate, $showStartedEvents);

        return $this->resolveRecurringEvents(
            $query->execute(),
            $grouped = false,
            $startDate,
            $stopDate,
            $showStartedEvents,
            $limit
        );
    }

    /**
     * Add conditions to retrieve recurring dates from the database
     *
     * @param \TYPO3\CMS\Extbase\Persistence\QueryInterface $query
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface $conditions
     * @param \DateTime $startDate
     * @param \DateTime $stopDate
     * @return void
     */
    protected function applyRecurringConditions(
        QueryInterface &$query,
        ConstraintInterface $conditions,
        \DateTime $startDate,
        \DateTime $stopDate
    ) {
        /** @var Query $query */
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
     * @param bool $grouped
     * @param \DateTime $startDate
     * @param \DateTime $stopDate
     * @param  boolean $skipTodayCheck
     * @param integer $limit
     * @return array $days
     */
    protected function resolveRecurringEvents(
        QueryResultInterface $events,
        $grouped,
        \DateTime $startDate,
        \DateTime $stopDate,
        $skipTodayCheck = false,
        $limit = null
    ) {
        $today = new \DateTime('midnight');
        $days = [];
        foreach ($events as $event) {
            /** @var \DateTime $eventDate */
            foreach ($event->getEventDates($startDate, $stopDate, $grouped) as $eventDate) {
                if (($grouped === false && $skipTodayCheck === false && $eventDate->format('U') < $today->format('U'))
                    || ($grouped === true && $eventDate->format('U') < $startDate->format('U'))
                ) {
                    continue;
                }
                $recurringEvent = clone($event);
                $recurringEvent->setEventDate($eventDate);
                if ($grouped) {
                    $days[$eventDate->format('Y-m-d')]['events'][$event->getUid()] = $recurringEvent;
                } else {
                    if ($recurringEvent->getEventStopDate() >= $today) {
                        $days[$eventDate->format('Y-m-d') . '_' . $event->getUniqueIdentifier()] = $recurringEvent;
                    }
                }
            }
        }
        ksort($days);

        if (!is_null($limit)) {
            $days = array_slice($days, 0, $limit, true);
        }

        return $days;
    }

    /**
     * Returns query constraints for simple events.
     *
     * @param   \TYPO3\CMS\Extbase\Persistence\QueryInterface $query
     * @param   \DateTime $startDate
     * @param   \DateTime $stopDate
     * @param   boolean $showStartedEvents
     * @return  \TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface  $conditions
     */
    protected function getBaseConditions(
        QueryInterface &$query,
        \DateTime $startDate,
        \DateTime $stopDate,
        $showStartedEvents = false
    ) {
        /** @var Query $query */
        $conditions = $query->logicalAnd(
            $query->greaterThanOrEqual('event_date', $startDate),
            $query->lessThanOrEqual('event_date', $stopDate)
        );
        if ($showStartedEvents == true) {
            $conditions = $query->logicalOr(
                $conditions,
                $query->logicalAnd(
                    $query->lessThanOrEqual('event_date', $startDate),
                    $query->greaterThanOrEqual('event_stop_date', $startDate)
                ),
                // Veranstaltung die im/nach dem Zeitfenster endet
                $query->greaterThanOrEqual('event_stop_date', $startDate),
                // Veranstaltung die das genze Zeitfenster beinhaltet
                $query->logicalAnd(
                    $query->lessThanOrEqual('event_date', $startDate),
                    $query->greaterThanOrEqual('event_stop_date', $stopDate)
                )
            );
        }

        return $conditions;
    }

    /**
     * Find all events between $startDate and $stopDate
     *
     * @param  \DateTime $startDate
     * @param  \DateTime $stopDate
     * @param  boolean $showStartedEvents
     * @return array      $events
     */
    protected function queryAllBetween(\DateTime $startDate, \DateTime $stopDate, $showStartedEvents = false)
    {
        $query = $this->createQuery();
        $query->setOrderings(['event_date' => QueryInterface::ORDER_ASCENDING]);
        $conditions = $this->getBaseConditions($query, $startDate, $stopDate, $showStartedEvents);
        $this->applyRecurringConditions($query, $conditions, $startDate, $stopDate);

        return $query;
    }
}
