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
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Testcase for class Tx_GbEvents_Domain_Model_Event.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage Terminkalender
 *
 * @author Morton Jonuschat <m.jonuschat@gute-botschafter.de>
 */
class Tx_GbEvents_Domain_Model_EventTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
  /**
   * @var Tx_GbEvents_Domain_Model_Event
   */
  protected $fixture;

  public function setUp() {
    $this->fixture = new Tx_GbEvents_Domain_Model_Event();
  }

  public function tearDown() {
    unset($this->fixture);
  }


  /**
   * @test
   */
  public function getTitleReturnsInitialValueForString() { }

    /**
     * @test
     */
    public function setTitleForStringSetsTitle() {
      $this->fixture->setTitle('Conceived at T3CON10');

      $this->assertSame(
        'Conceived at T3CON10',
        $this->fixture->getTitle()
      );
    }

  /**
   * @test
   */
  public function getTeaserReturnsInitialValueForString() { }

    /**
     * @test
     */
    public function setTeaserForStringSetsTeaser() {
      $this->fixture->setTeaser('Conceived at T3CON10');

      $this->assertSame(
        'Conceived at T3CON10',
        $this->fixture->getTeaser()
      );
    }

  /**
   * @test
   */
  public function getDescriptionReturnsInitialValueForString() { }

    /**
     * @test
     */
    public function setDescriptionForStringSetsDescription() {
      $this->fixture->setDescription('Conceived at T3CON10');

      $this->assertSame(
        'Conceived at T3CON10',
        $this->fixture->getDescription()
      );
    }

  /**
   * @test
   */
  public function getLocationReturnsInitialValueForString() { }

    /**
     * @test
     */
    public function setLocationForStringSetsLocation() {
      $this->fixture->setLocation('Conceived at T3CON10');

      $this->assertSame(
        'Conceived at T3CON10',
        $this->fixture->getLocation()
      );
    }

  /**
   * @test
   */
  public function getEventDateReturnsInitialValueForDateTime() { }

    /**
     * @test
     */
    public function setEventDateForDateTimeSetsEventDate() { }

    /**
     * @test
     */
    public function getEventTimeReturnsInitialValueForString() { }

    /**
     * @test
     */
    public function setEventTimeForStringSetsEventTime() {
      $this->fixture->setEventTime('Conceived at T3CON10');

      $this->assertSame(
        'Conceived at T3CON10',
        $this->fixture->getEventTime()
      );
    }

}
