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

class LimbRootGroupTest extends LimbGroupTest
{
  function limbRootGroupTest()
  {
    $this->limbGroupTest('all LIMB tests');
  }

  function getTestCasesHandles()
  {
    return TestFinder::getTestCasesHandlesFromDirectoryRecursive(LIMB_DIR . '/tests/groups');
  }
}

?>