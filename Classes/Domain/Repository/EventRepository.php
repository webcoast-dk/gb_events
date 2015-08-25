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

use GuteBotschafter\GbEvents\Domain\Model\Event;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Repository for GuteBotschafter\GbEvents\Domain\Model\Event
 */
class EventRepository extends Repository {
	/**
	 * Find all events between $startDate and $stopDate
	 *
	 * @param \DateTime $startDate
	 * @param \DateTime $stopDate
	 * @param bool $showStartedEvents
	 * @param string $categories
	 * @return array
	 */
	public function findAllBetween(\DateTime $startDate, \DateTime $stopDate, $showStartedEvents = FALSE, $categories = NULL) {
		$query = $this->queryAllBetween($startDate, $stopDate, $showStartedEvents, $categories);
		return $this->resolveRecurringEvents($query->execute(), $grouped = TRUE, $startDate, $stopDate, $showStartedEvents);
	}

	/**
	 * Find all events (limited to a amount of years)
	 *
	 * @param int $years
	 * @param bool $showStartedEvents
	 * @param string $categories
	 * @return array
	 */
	public function findAll($years = 1, $showStartedEvents = FALSE, $categories = NULL) {
		if ((int)$years === 0) {
			$years = 1;
		}

		$startDate = new \DateTime('midnight');
		$stopDate = new \DateTime(sprintf('midnight + %d years', intval($years)));

		$query = $this->queryAllBetween($startDate, $stopDate, $showStartedEvents, $categories);
		return $this->resolveRecurringEvents($query->execute(), $grouped = FALSE, $startDate, $stopDate, $showStartedEvents);
	}

	/**
	 * Find upcoming events (limited to a count of n)
	 *
	 * @param int $limit
	 * @param bool $showStartedEvents
	 * @param string $categories
	 * @return array
	 */
	public function findUpcoming($limit = 3, $showStartedEvents = FALSE, $categories = NULL) {
		if ((int)$limit === 0) {
			$limit = 3;
		}

		$startDate = new \DateTime('midnight');
		$stopDate = new \DateTime('midnight + 5 years');

		$query = $this->createQuery();
		$query->setOrderings(array('event_date' => QueryInterface::ORDER_ASCENDING));
		/** @var \TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface $conditions */
		$conditions = $query->greaterThanOrEqual('event_date', $startDate);
		$this->applyRecurringConditions($query, $conditions, $startDate, $stopDate, $categories);
		return $this->resolveRecurringEvents($query->execute(), $grouped = FALSE, $startDate, $stopDate, $showStartedEvents, $limit);
	}

	/**
	 * Find past events (limited to a count of n)
	 *
	 * @param int $limit
	 * @param string $categories
	 * @return array
	 */
	public function findBygone($limit = 3, $categories = NULL) {
		if ((int)$limit === 0) {
			$limit = 3;
		}

		$startDate = new \DateTime('midnight - 10 years');
		$stopDate = new \DateTime('midnight - 1 second');
		$cutOffDate = new \DateTime($limit . ' years ago midnight');

		$query = $this->createQuery();
		$query->setOrderings(array('event_date' => QueryInterface::ORDER_ASCENDING));
		/** @var \TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface $conditions */
		$conditions = $query->greaterThanOrEqual('event_date', $startDate);
		$this->applyRecurringConditions($query, $conditions, $startDate, $stopDate, $categories);
		$events = array_filter(
			$this->resolveRecurringEvents($query->execute(), $grouped = FALSE, $startDate, $stopDate, TRUE),
			function (Event $event) use (&$cutOffDate, &$stopDate) {
				return $event->getEventDate() >= $cutOffDate && $event->getEventDate() <= $stopDate;
			}
		);
		usort($events, function (Event $a, Event $b) {
			return strcmp($a->getEventDate()->getTimestamp(), $b->getEventDate()->getTimestamp());
		});
		return array_reverse($events);
	}

	/**
	 * Add conditions to retrieve recurring dates from the database
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\QueryInterface $query
	 * @param \TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface $conditions
	 * @param \DateTime $startDate
	 * @param \DateTime $stopDate
	 * @param string $categories
	 */
	protected function applyRecurringConditions(QueryInterface &$query, ConstraintInterface $conditions, \DateTime $startDate, \DateTime $stopDate, $categories = NULL) {
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
	 * @param \TYPO3\CMS\Extbase\Persistence\QueryInterface $query
	 * @param \TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface $conditions
	 * @param array $categories
	 */
	protected function applyCategoryFilters(QueryInterface $query, ConstraintInterface &$conditions, $categories) {
		$categories = GeneralUtility::intExplode(',', $categories, TRUE);
		if (is_array($categories) && !empty($categories)) {
			$categoryConditions = array_map(
				function ($category) use ($query) {
					return $query->contains('categories', $category);
				},
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
	 * @param \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $events
	 * @param bool $grouped
	 * @param \DateTime $startDate
	 * @param \DateTime $stopDate
	 * @param bool $checkDuration
	 * @param integer $limit
	 * @return array
	 */
	protected function resolveRecurringEvents(QueryResultInterface $events, $grouped = FALSE, \DateTime $startDate, \DateTime $stopDate, $checkDuration = FALSE, $limit = 0) {
		$today = new \DateTime('midnight today');
		$days = array();
		foreach ($events as $event) {
			foreach ($event->getEventDates($startDate, $stopDate, $grouped) as $eventDate) {
				/** @var \DateTime $eventDate */
				if ($grouped === FALSE) {
					if ($checkDuration === FALSE && !$this->isVisibleEvent($eventDate)) {
						continue;
					}
					if ($checkDuration === TRUE && !$this->isVisibleEvent($eventDate, $event->getDuration())) {
						continue;
					}
				} else {
					if (!$this->isVisibleEvent($eventDate, 0, $startDate)) {
						continue;
					}
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

		if ((int)$limit !== 0) {
			$days = array_slice($days, 0, $limit, TRUE);
		}

		return $days;
	}

	/**
	 * Check if the event is active at the given point in time
	 *
	 * @param \DateTime $eventDate
	 * @param integer $duration
	 * @param \DateTime $currentDate
	 * @return boolean
	 */
	protected function isVisibleEvent(\DateTime $eventDate, $duration = 0, \DateTime $currentDate = NULL) {
		if (is_null($currentDate)) {
			$currentDate = new \DateTime('midnight');
		}
		return ($eventDate->getTimestamp() + $duration) >= $currentDate->getTimestamp();
	}

	/**
	 * Returns query constraints for simple events.
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\QueryInterface $query
	 * @param \DateTime $startDate
	 * @param \DateTime $stopDate
	 * @param bool $showStartedEvents
	 * @return \TYPO3\CMS\Extbase\Persistence\Generic\Qom\AndInterface|\TYPO3\CMS\Extbase\Persistence\Generic\Qom\OrInterface
	 */
	protected function getBaseConditions(QueryInterface &$query, \DateTime $startDate, \DateTime $stopDate, $showStartedEvents = FALSE) {
		$conditions = $query->logicalAnd(
			$query->greaterThanOrEqual('event_date', $startDate),
			$query->lessThanOrEqual('event_date', $stopDate)
		);
		if ($showStartedEvents == TRUE) {
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
	 * @param \DateTime $startDate
	 * @param \DateTime $stopDate
	 * @param bool $showStartedEvents
	 * @param null $categories
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface
	 */
	protected function queryAllBetween(\DateTime $startDate, \DateTime $stopDate, $showStartedEvents = FALSE, $categories = NULL) {
		$query = $this->createQuery();
		$query->setOrderings(array('event_date' => QueryInterface::ORDER_ASCENDING));
		$conditions = $this->getBaseConditions($query, $startDate, $stopDate, $showStartedEvents);
		$this->applyRecurringConditions($query, $conditions, $startDate, $stopDate, $categories);
		return $query;
	}
}
