<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: LogTest.class.php 1028 2005-01-18 11:06:55Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/i18n/Locale.class.php');

class LocaleTest extends LimbTestCase
{
  function LocaleTest()
  {
    parent :: LimbTestCase('locale test');
  }

  function testGetMonthNameJanuary()
  {
    $locale = new Locale('en');
    $this->assertEqual($locale->getMonthName(0), 'January');
  }

  function testGetMonthNameFebruary()
  {
    $locale = new Locale('en');
    $this->assertEqual($locale->getMonthName(1), 'February');
  }

  function testGetWrongMonthName()
  {
    $locale = new Locale('en');
    $this->assertNull($locale->getMonthName(12));
  }

  function testGetDayName()
  {
    $locale = new Locale('en');

    $this->assertEqual($locale->getDayName(0, $short = false), 'Monday');
    $this->assertEqual($locale->getDayName(0, $short = true), 'Mon');
  }

  function testGetDayNameSunday()
  {
    $locale = new Locale('en');

    $this->assertEqual($locale->getDayName(6, $short = false), 'Sunday');
    $this->assertEqual($locale->getDayName(6, $short = true), 'Sun');
  }
}

?>