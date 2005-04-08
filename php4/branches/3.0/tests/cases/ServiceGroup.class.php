<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: RequestResolverGroup.class.php 1200 2005-04-04 09:13:42Z pachanga $
*
***********************************************************************************/
class ServiceGroup extends LimbGroupTest
{
  function ServiceGroup()
  {
    parent :: LimbGroupTest(__FILE__);
  }

  function getTestCasesHandles()
  {
    return TestFinder::getTestCasesHandlesFromDirectory(LIMB_DIR . '/tests/cases/services');
  }

}
?>