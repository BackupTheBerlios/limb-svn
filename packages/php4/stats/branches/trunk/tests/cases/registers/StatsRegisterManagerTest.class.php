<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_STATS_DIR . '/registers/StatsRegisterManager.class.php');
require_once(LIMB_STATS_DIR . '/registers/StatsRequest.class.php');
require_once(LIMB_STATS_DIR . '/registers/StatsRegister.interface.php');

Mock :: generate('StatsRegister');

class StatsRegisterManagerTest extends LimbTestCase
{
  function StatsRegisterManagerTest()
  {
    parent :: LimbTestCase('stats register manager test');
  }

  function testRegister()
  {
    $manager = new StatsRegisterManager();

    $register1 = new MockStatsRegister($this);
    $register2 = new MockStatsRegister($this);

    $stats_request = new StatsRequest();

    $manager->addRegister($register1);
    $manager->addRegister($register2);

    $register1->expectOnce('register', array($stats_request));
    $register2->expectOnce('register', array($stats_request));

    $manager->register($stats_request);
  }
}

?>