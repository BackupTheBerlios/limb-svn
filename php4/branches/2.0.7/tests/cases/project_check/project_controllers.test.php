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
require_once(LIMB_DIR . '/core/template/template.class.php');
require_once(LIMB_DIR . '/core/actions/action_factory.class.php');

class test_project_controllers extends UnitTestCase
{
	var $controllers = array();
	
	function test_project_controllers($name = 'controllers test case')
	{
		parent :: UnitTestCase($name);
	} 
	
	function test_controllers()
	{
		$this->_load_controllers(PROJECT_DIR . '/core/controllers/');
		$this->_load_controllers(LIMB_DIR . '/core/controllers/');
		
		$this->_check_all_controllers();
	}
		
	function _check_all_controllers()
	{
		foreach($this->controllers as $controller)
		{
			$this->_check_controller($controller);
		}
	}
		
	function _check_controller($controller)
	{
		$definitions = $controller->get_actions_definitions();
		$controller_class = get_class($controller);
		
		foreach($definitions as $action => $data)
		{
			$this->assertTrue(isset($data['permissions_required']), 
				'controller: "' . $controller_class . 
				'" permissions_required property for action "' . $action . '"not set');
			
			$this->assertTrue(in_array($data['permissions_required'], array('r', 'w', 'rw')), 
				'controller: "' . $controller_class . 
				'" permissions_required property for action "' . $action . '"not valid');
			
			if (isset($data['template_path']))
			{
				$template = new template($data['template_path']);
				$this->_check_template($template);
			}
			
			if(isset($data['action_path']))
			{	
				$action_obj = action_factory :: create($data['action_path']);
				
				$this->assertNotNull($action_obj, 
					'controller: "' . $controller_class . 
					'" action object for action "' . $action . '"not found');
			}
			
			if(isset($data['action_name']))
			{
				$this->assertTrue(($data['action_name']), 
					'controller: "' . $controller_class . 
					'" action_name property for action "' . $action . '" is empty - check strings');
			}
		}
		
		$action = $controller->get_default_action();
		
		$this->assertTrue(isset($definitions[$action]), 
			'controller: "' . $controller_class . 
			'" default action "' . $action . '" doesnt exist');
	}
	
	function _check_template($template)
	{
	}
	
	function _load_controllers($dir_name)
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
					
					$controller = new $class_name();
					$this->controllers[$class_name] = $controller;
				} 
			} 
			closedir($dir); 
		} 
	}
		
} 
?>