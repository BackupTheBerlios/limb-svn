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
require_once(LIMB_DIR . '/core/lib/db/db_factory.class.php');
require_once(LIMB_DIR . '/tests/cases/site_objects_testers/site_object_tester.class.php');
require_once(LIMB_DIR . '/tests/cases/site_objects_testers/content_object_tester.class.php');

class project_check_group extends GroupTest 
{
	function project_check_group() 
	{
	  $this->GroupTest('project tests');
	  
	  TestManager::addTestCasesFromDirectory($this, LIMB_DIR . '/tests/cases/project_check');
	  
	  $this->add_site_object_test_cases();	  
	}
	
	function add_site_object_test_cases()
	{
	  $loader = new project_site_objects_loader();
	  
	  foreach($loader->get_classes_list() as $class_name)
	  {
	  	$this->addTestCase($this->_get_site_object_test_case($class_name));
	  }
	}
	
	function &_get_site_object_test_case($class_name)
	{
		if(file_exists(PROJECT_DIR . '/tests/cases/site_objects_testers/' . $class_name . '_tester.class.php'))
		{
			include_once(PROJECT_DIR . '/tests/cases/site_objects_testers/' . $class_name . '_tester.class.php');
			$test_case_name = $class_name . '_tester';
			$test_case =& new $test_case_name($class_name);
		}
		elseif(file_exists(LIMB_DIR . '/tests/cases/site_objects_testers/' . $class_name . '_tester.class.php'))
		{
			include_once(LIMB_DIR . '/tests/cases/site_objects_testers/' . $class_name . '_tester.class.php');
			$test_case_name = $class_name . '_tester';
			$test_case =& new $test_case_name($class_name);
		}
		else
		{	
			$site_object = site_object_factory :: create($class_name);
					
	  	if (is_subclass_of($site_object, 'content_object'))
	  		$test_case =& new content_object_tester($class_name);
	  	else
	  		$test_case =& new site_object_tester($class_name);
		}
		
		return $test_case;
	}
}
?>