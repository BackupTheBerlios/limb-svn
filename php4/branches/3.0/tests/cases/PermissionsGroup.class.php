<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: CommandsGroup.class.php 1209 2005-04-08 14:29:41Z pachanga $
*
***********************************************************************************/
class PermissionsGroup extends LimbGroupTest
{
  function PermissionsGroup()
  {
    parent :: LimbGroupTest(__FILE__);
  }

  function getTestCasesHandles()
  {
    return TestFinder::getTestCasesHandlesFromDirectory(LIMB_DIR . '/tests/cases/permissions');
  }
}

?>