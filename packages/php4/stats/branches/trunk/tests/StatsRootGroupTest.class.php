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
require_once(LIMB_DIR . '/tests/lib/LimbGroupTest.class.php');

class StatsRootGroupTest extends LimbGroupTest
{
  function StatsRootGroupTest()
  {
    parent :: LimbGroupTest('stats package tests');
  }

  function getTestCasesHandles()
  {
    return TestFinder::getTestCasesHandlesFromDirectory(dirname(__FILE__) . '/cases/');
  }
}

?>