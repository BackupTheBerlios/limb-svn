<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/tests/lib/project_site_objects_loader.class.php');
require_once(LIMB_DIR . '/tests/lib/site_objects_test_manager.php');

class project_site_objects_check_group extends LimbGroupTest 
{
	function project_site_objects_check_group() 
	{
	  $this->GroupTest('LIMB application site objects tests');
	}	
	
	function getTestCasesHandles()
	{
    return SiteObjectsTestManager::getTestCasesHandlesWithLoader(new project_site_objects_loader());	  
	}
}
?>