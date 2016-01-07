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
 * UpcomingController
 */
class UpcomingController extends BaseController
{
    /**
     * Displays all Events
     *
     * @return void
     */
    public function listAction()
    {
        $events = $this->eventRepository->findUpcoming(
            $this->settings['limit'],
            (bool)$this->settings['showStartedEvents'],
            $this->settings['categories']
        );
        $this->addCacheTags($events, 'tx_gbevents_domain_model_event');
        $this->view->assign('events', $events);
    }
}
