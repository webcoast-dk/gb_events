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
 * A single event
 */
class Tx_GbEvents_Domain_Model_Event extends Tx_Extbase_DomainObject_AbstractEntity {

  /**
   * The title of the event
   *
   * @var string
   * @validate NotEmpty
   */
  protected $title;

  /**
   * A short teaser text
   *
   * @var string
   */
  protected $teaser;

  /**
   * A detailed description of the event
   *
   * @var string
   * @validate NotEmpty
   */
  protected $description;

  /**
   * The location of the event
   *
   * @var string
   */
  protected $location;

  /**
   * The date when the event happens
   *
   * @var DateTime
   * @validate NotEmpty
   */
  protected $eventDate;

  /**
   * The time the event happens
   *
   * @var string
   */
  protected $eventTime;

  /**
   * @param string $title
   * @return void
   */
  public function setTitle($title) {
    $this->title = $title;
  }

  /**
   * @return string
   */
  public function getTitle() {
    return $this->title;
  }

  /**
   * @param string $teaser
   * @return void
   */
  public function setTeaser($teaser) {
    $this->teaser = $teaser;
  }

  /**
   * @return string
   */
  public function getTeaser() {
    return $this->teaser;
  }

  /**
   * @param string $description
   * @return void
   */
  public function setDescription($description) {
    $this->description = $description;
  }

  /**
   * @return string
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * @param string $location
   * @return void
   */
  public function setLocation($location) {
    $this->location = $location;
  }

  /**
   * @return string
   */
  public function getLocation() {
    return $this->location;
  }

  /**
   * @param DateTime $eventDate
   * @return void
   */
  public function setEventDate(DateTime $eventDate) {
    $this->eventDate = $eventDate;
  }

  /**
   * @return DateTime
   */
  public function getEventDate() {
    return $this->eventDate;
  }

  /**
   * @param string $eventTime
   * @return void
   */
  public function setEventTime($eventTime) {
    $this->eventTime = $eventTime;
  }

  /**
   * @return string
   */
  public function getEventTime() {
    return $this->eventTime;
  }

}
