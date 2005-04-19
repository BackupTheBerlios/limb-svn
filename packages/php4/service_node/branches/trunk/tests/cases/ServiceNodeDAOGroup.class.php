<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: CommonSiteObjectsGroup.class.php 1075 2005-01-29 15:50:12Z pachanga $
*
***********************************************************************************/
class ServiceNodeDAOGroup extends LimbGroupTest
{
  function ServiceNodeDAOGroup()
  {
    parent :: LimbGroupTest(__FILE__);
  }

  function getTestCasesHandles()
  {
    return TestFinder::getTestCasesHandlesFromDirectory(dirname(__FILE__) . '/dao/');
  }
}

?>