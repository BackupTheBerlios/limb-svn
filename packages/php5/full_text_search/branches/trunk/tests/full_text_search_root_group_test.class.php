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
require_once(LIMB_DIR . '/tests/cases/limb_group_test.class.php');

class FullTextSearchRootGroupTest extends LimbGroupTest
{
  function FullTextSearchRootGroupTest()
  {
    $this->LimbGroupTest('full text search package tests');
  }

  function getTestCasesHandles()
  {
    return TestFinder::getTestCasesHandlesFromDirectoryRecursive(dirname(__FILE__) . '/groups');
  }
}

?>