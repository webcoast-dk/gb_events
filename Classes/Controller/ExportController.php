<?php
namespace GuteBotschafter\GbEvents\Controller;

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
 * ExportController
 */
class ExportController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {
  /**
   * Prefix for iCalendar files
   */
  const VCALENDAR_START = "BEGIN:VCALENDAR\nVERSION:2.0\nPRODID:gb_events TYPO3 Extension\nMETHOD:PUBLISH";

  /**
   * Postfix for iCalendar files
   */
  const VCALENDAR_END   = "END:VCALENDAR";

  /**
   * @var \GuteBotschafter\GbEvents\Domain\Repository\EventRepository
   * @inject
   */
  protected $eventRepository;

  /**
   * Displays all Events
   *
   * @return string The rendered view
   */
  public function listAction() {
    $events = $this->eventRepository->findAll($this->settings['years'], (bool)$this->settings['showStartedEvents'], $this->settings['categories']);
    $content = array();
    foreach ($events as $event) {
      $content[$event->getUniqueIdentifier()] = $event->iCalendarData(FALSE);
    }
    $this->renderCalendar(join("\n", $content));
  }

  /**
   * Exports a single Event as iCalendar file
   *
   * @param \GuteBotschafter\GbEvents\Domain\Model\Event $event the Event to export
   * @return void
   */
  public function showAction(\GuteBotschafter\GbEvents\Domain\Model\Event $event) {
    $this->renderCalendar($event->iCalendarData(), $event->iCalendarFilename());
  }

  /**
   * Set content headers for the iCalendar data
   *
   * @param  string $data
   * @param  string $filename
   * @return void
   */
  protected function setHeaders($content, $filename) {
    if(ob_get_contents()) {
      throw new \Exception('Some data has already been sent to the browser', 1408607681);
    }
    header('Content-Type: text/calendar');
    if(headers_sent()) {
      throw new \Exception('Some data has already been sent to the browser', 1408607681);
    }

    header('Cache-Control: public');
    header('Pragma: public');
    header('Content-Description: iCalendar Event File');
    header('Content-Transfer-Encoding: binary');
    header('Content-Disposition: attachment; filename="' . $filename .'"');
    if (!isset($_SERVER['HTTP_ACCEPT_ENCODING']) OR empty($_SERVER['HTTP_ACCEPT_ENCODING'])) {
      header('Content-Length: '.strlen($content));
    }
  }

  /**
   * Render the iCalendar events with the required wrap
   *
   * @param  string $events
   * @param  string $filename
   * @return void
   */
  protected function renderCalendar($events, $filename = 'calendar.ics') {
    if(trim($events) === '') {
      throw new \Exception('No events to process', 1408611856);
    }
    $content = join("\n", array(
      ExportController::VCALENDAR_START,
      $events,
      ExportController::VCALENDAR_END
    ));
    $this->setHeaders($content, $filename);

    echo $content;
    die;
  }
}
