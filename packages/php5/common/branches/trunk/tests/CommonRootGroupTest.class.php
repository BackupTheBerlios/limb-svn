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
require_once(LIMB_DIR . '/tests/cases/LimbGroupTest.class.php');

class CommonRootGroupTest extends LimbGroupTest
{
  function commonRootGroupTest()
  {
    $this->limbGroupTest('common package tests');
  }

  function getTestCasesHandles()
  {
    return TestFinder::getTestCasesHandlesFromDirectoryRecursive(dirname(__FILE__) . '/groups');
  }
}

?>