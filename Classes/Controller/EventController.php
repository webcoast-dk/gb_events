<?php
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
 * Controller for the Event object
 */
class Tx_GbEvents_Controller_EventController extends Tx_Extbase_MVC_Controller_ActionController {

  /**
   * @var Tx_GbEvents_Domain_Repository_EventRepository
   */
  protected $eventRepository;

  /**
   * @param Tx_GbEvents_Domain_Repository_EventRepository $eventRepository
   * @return void
   */
  public function injectEventRepository(Tx_GbEvents_Domain_Repository_EventRepository $eventRepository) {
    $this->eventRepository = $eventRepository;
  }


  /**
   * Displays all Events
   *
   * @return void
   */
  public function listAction() {
    $events = $this->eventRepository->findAll();
    $this->view->assign('events', $events);
  }


  /**
   * Displays all Events as a browseable calendar
   *
   * @param string $start
   * @return void
   */
  public function calendarAction($start = 'today') {
    // Startdatum setzen
    $startDate = new DateTime('today');
    try {
      $startDate->modify($start);
    } catch(Exception $e) {
      $startDate->modify('midnight');
    }

    // Start für Kalenderanzeige bestimmen
    $preDate = clone($startDate);
    if($startDate->format("N") !== 1) {
      $preDate->modify('last monday of previous month');
    }

    // Ende des Monats bestimmen
    $stopDate = clone($startDate);
    $stopDate->modify('last day of this month');
    $stopDate->modify('+86399 seconds');

    $postDate = clone($stopDate);
    if($stopDate->format("N") !== 7) {
      $postDate->modify('next sunday');
    }

    // Navigational dates
    $nextMonth = clone($startDate);
    $nextMonth->modify('first day of next month');
    $previousMonth = clone($startDate);
    $previousMonth->modify('first day of previous month');

    $days = array();
    $runDate = clone($preDate);
    while($runDate <= $postDate) {
      $days[$runDate->format("Y-m-d")] = array('date' => clone($runDate), 'events' => array(), 'disabled' => (($runDate < $startDate) || ($runDate > $stopDate)));
      $runDate->modify('tomorrow');
    }

    $events = $this->eventRepository->findAllBetween($preDate, $postDate);
    foreach($events as $event) {
      foreach($event->getEventDates($preDate, $postDate) as $eventDate) {
        $days[$eventDate->format('Y-m-d')]['events'][$event->getUid()] = $event;
      }
    }

    $weeks = array();
    for($i = 0; $i < floor(count($days)/7); $i++) {
      $weeks[] = array_slice($days, $i*7, 7, TRUE);
    }

    $this->view->assignMultiple(array(
      'calendar' => $weeks,
      'nextMonth' => $nextMonth->format('Y-m-d'),
      'prevMonth' => $previousMonth->format('Y-m-d')
    ));
  }


  /**
   * Displays a single Event
   *
   * @param Tx_GbEvents_Domain_Model_Event $event the Event to display
   * @return string The rendered view
   */
  public function showAction(Tx_GbEvents_Domain_Model_Event $event) {
    $this->view->assign('event', $event);
  }


  /**
   * Displays the upcoming events
   *
   * @param Tx_GbEvents_Domain_Model_Event $event the Event to display
   * @return string The rendered view
   */
  public function upcomingAction() {
    $events = $this->eventRepository->findUpcoming($this->settings['limit']);
    $this->view->assign('events', $events);
  }


  /**
   * Exports a single Event as iCalendar file
   *
   * @param Tx_GbEvents_Domain_Model_Event $event the Event to export
   * @return string The rendered view
   */
  public function exportAction(Tx_GbEvents_Domain_Model_Event $event) {
    $this->response->setHeader('Cache-control', 'public', TRUE);
    $this->response->setHeader('Content-Description', 'iCalendar Event File', TRUE);
    $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $event->iCalendarFilename(). '"', TRUE);
    $this->response->setHeader('Content-Type', 'text/calendar', TRUE);
    $this->response->setHeader('Content-Transfer-Encoding', 'binary', TRUE);
    $this->response->sendHeaders();

    // $this->media is my domain model, add you own file path here :-)
    echo $event->iCalendarData();
    exit();
  }
}
