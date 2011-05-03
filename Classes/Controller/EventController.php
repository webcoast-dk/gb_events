<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Morton Jonuschat <m.jonuschat@gute-botschafter.de>, Gute Botschafter GmbH
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
	 * Displays all Events as a browseable calendard
	 *
	 * @return void
	 */
	public function calendarAction() {
		$events = $this->eventRepository->findAll();
		$this->view->assign('events', $events);
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
  public function upcomingActions() {
    $events = $this->eventRepository->findUpcoming();
		$this->view->assign('events', $events);
	}
}
