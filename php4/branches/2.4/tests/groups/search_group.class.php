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
class search_group extends LimbGroupTest
{
  function search_group()
  {
    parent :: LimbGroupTest('search tests');
  }

  function & getTestCasesHandles()
  {
    return TestFinder :: getTestCasesHandlesFromDirectoryRecursive(LIMB_DIR . '/tests/cases/full_text_search');
  }
}
?>