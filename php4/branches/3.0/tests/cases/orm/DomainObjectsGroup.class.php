<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: DomainObjectsGroup.class.php 1074 2005-01-29 15:46:16Z pachanga $
*
***********************************************************************************/
class DomainObjectsGroup extends LimbGroupTest
{
  function DomainObjectsGroup()
  {
    parent :: LimbGroupTest('domain objects tests');
  }

  function getTestCasesHandles()
  {
    return TestFinder::getTestCasesHandlesFromDirectoryRecursive(LIMB_DIR . '/tests/cases/orm/domain_objects');
  }
}
?>