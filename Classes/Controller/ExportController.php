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
   * @var \GuteBotschafter\GbEvents\Domain\Repository\EventRepository
   * @inject
   */
  protected $eventRepository;

  /**
   * Displays all Events
   *
   * @return \string The rendered view
   */
  public function listAction() {
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
    header('Content-Disposition: attachment; filename="calendar.ics"');
    header('Content-Transfer-Encoding: binary');

    $content = array(
      'BEGIN:VCALENDAR',
      'VERSION:2.0',
      'PRODID:gb_events TYPO3 Extension',
      'METHOD:PUBLIS',
    );

    $events = $this->eventRepository->findAll($this->settings['years']);
    foreach ($events as $event) {
      $content[$event->getUniqueIdentifier()] = $event->iCalendarData(FALSE);
    }
    $content[] = 'END:VCALENDAR';
    echo join("\n", $content);
    die;
  }

  /**
   * Exports a single Event as iCalendar file
   *
   * @param \GuteBotschafter\GbEvents\Domain\Model\Event $event the Event to export
   * @return void
   */
  public function showAction(\GuteBotschafter\GbEvents\Domain\Model\Event $event) {
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
    header('Content-Disposition: attachment; filename="' . $event->iCalendarFilename() .'"');
    header('Content-Transfer-Encoding: binary');

    if (!isset($_SERVER['HTTP_ACCEPT_ENCODING']) OR empty($_SERVER['HTTP_ACCEPT_ENCODING'])) {
      header('Content-Length: '.strlen($event->iCalendarData()));
    }
    echo $event->iCalendarData();
    die;
  }
}
