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
require_once(LIMB_DIR . '/core/date/Date.class.php');

class DateTest extends LimbTestCase
{
  function DateTest()
  {
    parent :: LimbTestCase('date test');
  }

  function testFormat()
  {
    $date = Date ::create($year = 2005, $month = 1, $day=20, $hour=10, $minute=15, $second=30);

    $toolkit = Limb :: toolkit();
    $locale =& $toolkit->getLocale('en');

    $formated_date = $date->format($locale, $locale->getDateFormat());

    $expected = 'Thursday 20 January 2005';
    $this->assertEqual($formated_date, $expected);
  }

  function testGetDayOfWeek()
  {
    $date = Date ::create($year = 2005, $month = 1, $day=20, $hour=10, $minute=15, $second=30);

    $formated_date = $date->getDayOfWeek();

    $expected = 'Thursday';
    $this->assertEqual($formated_date, 4);
  }

  function testSetByLocaleString()
  {
    $date = new Date();

    $locale = new Locale('en');
    $date->setByLocaleString($locale, 'Thursday 20 January 2005', '%A %d %B %Y');

    $this->assertEqual($date->getMonth(), 1);
    $this->assertEqual($date->getYear(), 2005);
    $this->assertEqual($date->getDay(), 20);
  }

  function testSetByLocaleStringShort()
  {
    $date = new Date();

    $locale = new Locale('en');
    $date->setByLocaleString($locale, 'Thu 20 Jan 2005', '%a %d %b %Y');

    $this->assertEqual($date->getMonth(), 1);
    $this->assertEqual($date->getYear(), 2005);
    $this->assertEqual($date->getDay(), 20);
  }
}

?>