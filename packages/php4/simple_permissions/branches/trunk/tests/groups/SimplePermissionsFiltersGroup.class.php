<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: simple_authorizer_group.class.php 783 2004-10-09 12:23:48Z pachanga $
*
***********************************************************************************/
class SimplePermissionsFiltersGroup extends LimbGroupTest
{
  function SimplePermissionsFiltersGroup()
  {
    parent :: LimbGroupTest('simple permissions filters tests');
  }

  function getTestCasesHandles()
  {
    return TestFinder::getTestCasesHandlesFromDirectoryRecursive(dirname(__FILE__) . '/../cases/filters');
  }
}
?>