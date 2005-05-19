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

class http_group extends LimbGroupTest
{
  function http_group()
  {
    parent :: LimbGroupTest('http tests');
  }

  function & getTestCasesHandles()
  {
    return TestFinder :: getTestCasesHandlesFromDirectoryRecursive(LIMB_DIR . '/tests/cases/http');
  }
}
?>