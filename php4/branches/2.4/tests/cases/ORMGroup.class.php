<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: DAOGroup.class.php 1073 2005-01-29 15:01:02Z pachanga $
*
***********************************************************************************/
class ORMGroup extends LimbGroupTest
{
  function ORMGroup()
  {
    parent :: LimbGroupTest('ORM tests');
  }

  function getTestCasesHandles()
  {
    return TestFinder::getTestCasesHandlesFromDirectory(LIMB_DIR . '/tests/cases/orm');
  }
}
?>