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
require_once(LIMB_DIR . '/core/lib/system/dir.class.php');
require_once(LIMB_DIR . '/core/actions/action_factory.class.php');

class test_form_site_object_actions extends UnitTestCase
{
	var $actions = array();
	
	function test_form_site_object_actions($name = 'form site object actions test case')
	{
		parent :: UnitTestCase($name);
	} 
	
	function test_actions()
	{
		$this->_load_actions(PROJECT_DIR . '/core/actions/');
		$this->_load_actions(LIMB_DIR . '/core/actions/');
		
		$this->_check_all_actions();
	}
		
	function _check_all_actions()
	{
		foreach($this->actions as $action)
			$this->_check_action($action);
	}
		
	function _check_action($action)
	{
		if(!is_subclass_of($action, 'form_create_site_object_action') &&
				!is_subclass_of($action, 'form_edit_site_object_action'))
		return;		

		$definition = $action->get_definition();
		$site_object = $action->get_site_object();

		$action->_init_validator(); //this is not a very good idea...
		$validator = $action->get_validator();
		
		$rules = $validator->get_rules();
		
		foreach($rules as $rule)
		{
			if(!is_subclass_of($rule, 'single_field_rule'))
				continue;
				
			$field_name = $rule->get_field_name();
			
			$this->assertTrue(isset($definition['datamap'][$field_name]),
				'no such field in datamap(validator rule) "' . $field_name. '" in "' . get_class($action) . '"' );
		}
	}
	
	function _load_actions($dir_name)
	{
		dir :: walk_dir($dir_name, array(&$this, '_add_action'));
	}
	
	function _add_action($dir, $file, $params)
	{
		if  (substr($file, -10,  10) == '.class.php')
		{
			$class_name = substr($file, 0, strpos($file, '.'));
			
			include_once($dir . $file);
			
			if (!isset($this->actions[$class_name]))
			{
				$object = new $class_name();
				
				if(is_subclass_of($object, 'action'))
					$this->actions[$class_name] = $object;
			}
		}
	}
		
} 
?>