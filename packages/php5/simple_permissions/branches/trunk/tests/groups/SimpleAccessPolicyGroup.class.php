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
class SimpleAccessPolicyGroup extends LimbGroupTest
{
  function simpleAccessPolicyGroup()
  {
    $this->limbGroupTest('simple permissions access policy tests');
  }

  function getTestCasesHandles()
  {
    return TestFinder::getTestCasesHandlesFromDirectoryRecursive(dirname(__FILE__) . '/../cases/access_policy');
  }
}
?>