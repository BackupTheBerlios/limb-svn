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
class full_text_search_indexers_group extends LimbGroupTest
{
  function full_text_search_indexers_group()
  {
    $this->LimbGroupTest('full text search indexers tests');
  }

  function getTestCasesHandles()
  {
    return TestFinder::getTestCasesHandlesFromDirectoryRecursive(dirname(__FILE__) . '/../cases/indexers/');
  }
}
?>