<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: action_factory.class.php 570 2004-02-26 12:37:31Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/lib/debug/debug.class.php');

class action_factory
{
	function action_factory()
	{
	}
	
	function get_class_name($class_path)
	{
		$pos = strrpos($class_path, '/');
		
		if($pos !== false)
			$class_name = substr($class_path, $pos + 1);
		else
			$class_name = $class_path;
			
		return $class_name;
	}
		
	function & create($class_path)
	{
		$class_name = action_factory :: get_class_name($class_path);
				
		if (file_exists(PROJECT_DIR . '/core/actions/' . $class_path . '.class.php')) 
			$full_path = PROJECT_DIR . '/core/actions/' . $class_path . '.class.php';
		elseif(file_exists(LIMB_DIR . '/core/actions/' . $class_path . '.class.php'))
			$full_path = LIMB_DIR . '/core/actions/' . $class_path . '.class.php';
		else
		{
			debug :: write_error('action not found', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('class_path' => $class_path));
			return null;
		}
			
		include_once($full_path);
	  $action =& new $class_name();
	  
	  return $action;
	}
}


?>