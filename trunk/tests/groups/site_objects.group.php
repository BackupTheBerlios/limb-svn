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
require_once(LIMB_DIR . '/core/lib/db/db_factory.class.php');
require_once(LIMB_DIR . '/tests/cases/site_objects/site_object_tester.class.php');
require_once(LIMB_DIR . '/tests/cases/site_objects/content_object_tester.class.php');

class tests_site_objects extends GroupTest 
{
	function tests_site_objects() 
	{
	  $this->GroupTest('site objects tests');
	  
	  foreach($this->_get_classes_list() as $row)
	  {
	  	$this->addTestCase($this->_get_site_object_test_case($row['class_name']));
	  }
	}
	
	function &_get_site_object_test_case($class_name)
	{
		if(file_exists(PROJECT_DIR . '/tests/cases/site_objects/' . $class_name . '_tester.class.php'))
		{
			include_once(PROJECT_DIR . '/tests/cases/site_objects/' . $class_name . '_tester.class.php');
			$test_case_name = $class_name . '_tester';
			$test_case =& new $test_case_name();
		}
		elseif(file_exists(LIMB_DIR . '/tests/cases/site_objects/' . $class_name . '_tester.class.php'))
		{
			include_once(LIMB_DIR . '/tests/cases/site_objects/' . $class_name . '_tester.class.php');
			$test_case_name = $class_name . '_tester';
			$test_case =& new $test_case_name();
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
	
	function _get_classes_list()
	{
		$project_db = str_replace('_tests', '', DB_NAME);
		
		$db =& db_factory :: instance();
		
		$db->select_db($project_db);
		
		$db->sql_select('sys_class');
		
		$list = $db->get_array();
		
		$db->select_db(DB_NAME);
		
		return $list;
	}
}
?>