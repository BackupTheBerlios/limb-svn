<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

class LimbRootGroupTest extends LimbGroupTest
{
  function LimbRootGroupTest()
  {
    parent :: LimbGroupTest('all LIMB tests');
  }

  function getTestCasesHandles()
  {
    return TestFinder::getTestCasesHandlesFromDirectoryRecursive(LIMB_DIR . '/tests/groups');
  }
}

?>