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

use GuteBotschafter\GbEvents\Domain\Model\Event;
use GuteBotschafter\GbEvents\Utility\ICalUtility;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\HttpUtility;

/**
 * ExportController
 */
class ExportController extends BaseController
{
    /**
     * Prefix for iCalendar files
     */
    const VCALENDAR_START = "BEGIN:VCALENDAR\nVERSION:2.0\nPRODID:gb_events TYPO3 Extension\nMETHOD:PUBLISH";

    /**
     * Postfix for iCalendar files
     */
    const VCALENDAR_END = "END:VCALENDAR";

    /**
     * Displays all Events
     */
    public function listAction(): ResponseInterface
    {
        $events = $this->eventRepository->findAll(
            $this->settings['years'],
            (bool)$this->settings['showStartedEvents'],
            $this->settings['categories']
        );
        $content = [];
        foreach ($events as $event) {
            /** @var Event $event */
            $content[$event->getUniqueIdentifier()] = ICalUtility::iCalendarData($event);
        }
        $this->addCacheTags($events, 'tx_gbevents_domain_model_event');

        return $this->renderCalendar(join("\n", $content));
    }

    /**
     * Exports a single Event as iCalendar file
     */
    public function showAction(Event $event)
    {
        $this->addCacheTags($event);

        return $this->renderCalendar(ICalUtility::iCalendarData($event), ICalUtility::iCalendarFilename($event));
    }

    /**
     * Set content headers for the iCalendar data
     */
    protected function setHeaders(ResponseInterface $response, string $content, string $filename): ResponseInterface
    {
        if (ob_get_contents()) {
            throw new \Exception('Some data has already been sent to the browser', 1408607681);
        }

        $response = $response
            ->withHeader('Content-Type', 'text/calendar')
            ->withHeader('Content-Transfer-Encoding', 'binary')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->withHeader('Cache-Control', 'public')
            ->withHeader('Pragma', 'public')
            ->withHeader('Content-Description', 'iCalendar Event File');

        if (!isset($_SERVER['HTTP_ACCEPT_ENCODING']) or empty($_SERVER['HTTP_ACCEPT_ENCODING'])) {
            $response = $response->withHeader('Content-Length', strlen($content));
        }

        return $response;
    }

    /**
     * Render the iCalendar events with the required wrap
     */
    protected function renderCalendar(string $events, string $filename = 'calendar.ics'): ResponseInterface
    {
        if (trim($events) === '') {
            throw new \Exception('No events to process', 1408611856);
        }
        $content = join("\n", [
            ExportController::VCALENDAR_START,
            $events,
            ExportController::VCALENDAR_END,
        ]);

        $response = $this->responseFactory->createResponse();
        $response->getBody()->write($content);

        return $this->setHeaders($response, $content, $filename);
    }
}
