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
 * Repository for Tx_GbEvents_Domain_Model_Event
 */
class Tx_GbEvents_Domain_Repository_EventRepository extends Tx_Extbase_Persistence_Repository {
  public function findAllBetween(DateTime $startDate, DateTime $stopDate) {
    $query = $this->createQuery();
    $query->setOrderings(array('event_date' => Tx_Extbase_Persistence_QueryInterface::ORDER_ASCENDING));
    $query->matching(
      $query->logicalOr(
        # Einzelne Veranstaltung im gesuchten Zeitfenster
        $query->logicalAnd(
          $query->greaterThanOrEqual('event_date', $startDate),
          $query->lessThanOrEqual('event_date', $stopDate)
        ),
        # Wiederkehrende Veranstaltung
        $query->logicalAnd(
          # Beginnt vor dem Ende des gesuchten Zeitraums
          $query->lessThanOrEqual('event_date', $stopDate),
          # Mindestens ein Wiederholungskriterium gesetzt
          $query->logicalOr(
            $query->greaterThan('recurringDays', 0),
            $query->greaterThan('recurringWeeks', 0)
          ),
          # Kein Enddatum oder Enddatum im/nach dem gesuchten Startdatum
          $query->logicalOr(
            $query->equals('recurringStop', 0),
            $query->greaterThanOrEqual('recurringStop', $startDate)
          )
        )
      )
    );
    return $query->execute();
  }

  public function findAll() {
    $query = $this->createQuery();
    $query->setOrderings(array('event_date' => Tx_Extbase_Persistence_QueryInterface::ORDER_ASCENDING));
    $query->matching(
      $query->greaterThanOrEqual('event_date', new DateTime('midnight'))
    );
    return $query->execute();
  }

  public function findUpcoming($limit = 3) {
    $query = $this->createQuery();
    $query->setOrderings(array('event_date' => Tx_Extbase_Persistence_QueryInterface::ORDER_ASCENDING));
    $query->setLimit(intval($limit));
    $query->matching(
      $query->greaterThanOrEqual('event_date', new DateTime('midnight'))
    );
    return $query->execute();
  }
}
