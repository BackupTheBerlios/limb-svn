<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: CommandsGroup.class.php 1102 2005-02-14 12:36:26Z pachanga $
*
***********************************************************************************/
class SimpleObjectCommandsGroup extends LimbGroupTest
{
  function SimpleObjectCommandsGroup()
  {
    parent :: LimbGroupTest('commands for simple objects manipulation tests');
  }

  function getTestCasesHandles()
  {
    return TestFinder::getTestCasesHandlesFromDirectory(LIMB_DIR . '/tests/cases/commands/simple_object_commands');
  }
}

?>