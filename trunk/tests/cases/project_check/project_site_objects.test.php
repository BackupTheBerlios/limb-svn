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
require_once(LIMB_DIR . '/core/lib/util/complex_array.class.php');
require_once(LIMB_DIR . '/core/template/fileschemes/simpleroot/compiler_support.inc.php');
require_once(LIMB_DIR . '/core/model/site_objects/content_object.class.php');

class content_object_test_adapter extends content_object
{
	var $content_object = null;
	
	function content_object_test_adapter($content_object)
	{
		$this->content_object = $content_object;
		
		parent :: content_object();
	}
	
	function _define_class_properties()
	{
		$props = $this->content_object->get_class_properties();
		
		if(!isset($props['db_table_name']))
			$props['db_table_name'] = get_class($this->content_object);
		
		return $props;
	}
	
	function get_db_table()
	{
		return $this->_get_db_table();
	}
}

class test_project_site_objects extends UnitTestCase
{
	var $site_objects = array();
	
	function test_project_site_objects($name = 'site objects test case')
	{
		parent :: UnitTestCase($name);
	} 
	
	function test_site_objects()
	{
		$this->_load_site_objects(PROJECT_DIR . '/core/model/site_objects/');
		$this->_load_site_objects(LIMB_DIR . '/core/model/site_objects/');
		
		$this->_check_all_site_objects();
	}
		
	function _check_all_site_objects()
	{
		foreach($this->site_objects as $site_object)
		{
			$this->_check_site_object($site_object);
		}
	}
		
	function _check_site_object($site_object)
	{
		$props = $site_object->get_class_properties();
		
		if(isset($props['abstract_class']) && $props['abstract_class'])
			return;
		
		if(isset($props['controller_class_name']))
		{
			$this->assertEqual(get_class($site_object->get_controller()), $props['controller_class_name']);
		}
		
		if (is_subclass_of($site_object, 'content_object'))
		{
			$content_object_adapter = new content_object_test_adapter($site_object);
			$db_table = $content_object_adapter->get_db_table();

			if(isset($props['db_table_name']))
			{
				$this->assertTrue(is_a($db_table, $props['db_table_name'].'_db_table'));
			}
			else
			{
				$this->assertTrue(is_a($db_table, get_class($site_object).'_db_table'));
			}
		}
	}
	
	function _load_site_objects($dir_name)
	{
		if ($dir = opendir($dir_name))
		{  
			while(($object_file = readdir($dir)) !== false) 
			{  
				if  (substr($object_file, -10,  10) == '.class.php')
				{
					$class_name = substr($object_file, 0, strpos($object_file, '.'));
					
					if(class_exists($class_name))
						continue;

					include_once($dir_name . '/' . $object_file);
					
					$site_object = new $class_name();
					$this->site_objects[$class_name] = $site_object;
				} 
			} 
			closedir($dir); 
		} 
	}
		
} 
?>