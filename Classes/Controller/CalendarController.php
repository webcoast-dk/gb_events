<?php
namespace GuteBotschafter\GbEvents\Controller;

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

/**
 * Controller for the calendar view
 */
class CalendarController extends BaseController {
	/**
	 * Displays all events as a browseable calendar
	 *
	 * @param  string $start
	 * @return void
	 */
	public function showAction($start = 'today') {
		// Startdatum setzen
		$startDate = new \DateTime('today');
		try {
			$startDate->modify($start);
		} catch (\Exception $e) {
			$startDate->modify('midnight');
		}

		// Start für Kalenderanzeige bestimmen
		$preDate = clone($startDate);
		if ($startDate->format('N') !== 1) {
			$preDate->modify('last monday of previous month');
		}

		// Ende des Monats bestimmen
		$stopDate = clone($startDate);
		$stopDate->modify('last day of this month');
		$stopDate->modify('+86399 seconds');

		$postDate = clone($stopDate);
		if ($stopDate->format('N') !== 7) {
			$postDate->modify('next sunday');
		}

		// Navigational dates
		$nextMonth = clone($startDate);
		$nextMonth->modify('first day of next month');
		$previousMonth = clone($startDate);
		$previousMonth->modify('first day of previous month');

		$days = array();
		$runDate = clone($preDate);
		while ($runDate <= $postDate) {
			$days[$runDate->format('Y-m-d')] = array('date' => clone($runDate), 'events' => array(), 'disabled' => (($runDate < $startDate) || ($runDate > $stopDate)));
			$runDate->modify('tomorrow');
		}

		$events = $this->eventRepository->findAllBetween($preDate, $postDate, FALSE, $this->settings['categories']);
		foreach ($events as $eventDay => $eventsThisDay) {
			$days[$eventDay]['events'] = $eventsThisDay['events'];
		}

		$weeks = array();
		$visibleWeeks = floor(count($days) / 7);
		for ($i = 0; $i < $visibleWeeks; $i++) {
			$weeks[] = array_slice($days, $i * 7, 7, TRUE);
		}

		$this->addCacheTags($events, 'tx_gbevents_domain_model_event');
		$this->view->assignMultiple(array(
			'calendar' => $weeks,
			'navigation' => array(
				'previous' => $previousMonth,
				'current' => $startDate,
				'next' => $nextMonth
			),
			'nextMonth' => $nextMonth->format('Y-m-d'),
			'prevMonth' => $previousMonth->format('Y-m-d')
		));
	}
}
