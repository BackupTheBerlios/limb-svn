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
require_once(LIMB_DIR . '/tests/lib/all_site_objects_loader.class.php');
require_once(LIMB_DIR . '/tests/lib/site_objects_test_manager.php');
require_once(LIMB_DIR . '/core/lib/db/db_factory.class.php');
require_once(LIMB_DIR . '/tests/cases/site_objects_testers/site_object_tester.class.php');
require_once(LIMB_DIR . '/tests/cases/site_objects_testers/content_object_tester.class.php');

class all_site_objects_check_group extends GroupTest 
{
	function all_site_objects_check_group() 
	{
	  $this->GroupTest('all site objects tests');
	  
	  SiteObjectsTestManager::addTestCasesWithLoader($this, new all_site_objects_loader(), 'controller');
	}	
}
?>