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
require_once(LIMB_DIR . '/class/lib/db/LimbDbPool.class.php');
require_once(LIMB_DIR . '/class/lib/util/ini_support.inc.php');

class LimbDbPoolTest extends LimbTestCase
{
  function LimbDbPoolTest()
  {
    parent :: LimbTestCase('db factory test case');
  }

  function testInstance()
  {
    $this->assertTrue(LimbDbPool :: getConnection() === LimbDbPool :: getConnection());
  }
}
?>