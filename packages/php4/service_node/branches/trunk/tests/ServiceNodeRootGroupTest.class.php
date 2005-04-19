<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: CommonRootGroupTest.class.php 1075 2005-01-29 15:50:12Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/tests/lib/LimbGroupTest.class.php');

class ServiceNodeRootGroupTest extends LimbGroupTest
{
  function ServiceNodeRootGroupTest()
  {
    parent :: LimbGroupTest(__FILE__);
  }

  function getTestCasesHandles()
  {
    return TestFinder::getTestCasesHandlesFromDirectory(dirname(__FILE__) . '/cases');
  }
}

?>