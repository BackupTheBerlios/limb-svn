<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

class fetching_group extends LimbGroupTest
{
  function fetching_group()
  {
    parent :: LimbGroupTest('fetch operations');
  }

  function & getTestCasesHandles()
  {
    return TestFinder :: getTestCasesHandlesFromDirectoryRecursive(LIMB_DIR . '/tests/cases/fetching');
  }
}
?>