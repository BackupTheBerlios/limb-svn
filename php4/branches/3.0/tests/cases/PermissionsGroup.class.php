<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: TreeGroup.class.php 1102 2005-02-14 12:36:26Z pachanga $
*
***********************************************************************************/
class PermissionsGroup extends LimbGroupTest
{
  function PermissionsGroup()
  {
    parent :: LimbGroupTest('permissions related tests');
  }

  function getTestCasesHandles()
  {
    return TestFinder::getTestCasesHandlesFromDirectory(LIMB_DIR . '/tests/cases/permissions');
  }
}
?>