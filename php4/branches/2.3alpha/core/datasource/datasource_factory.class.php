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
require_once(LIMB_DIR . '/core/lib/debug/debug.class.php');

class datasource_factory
{
	function datasource_factory()
	{
	}
		
	function & create($class_path)
	{
		$pos = strrpos($class_path, '/');
		
		if($pos !== false)
			$class_name = substr($class_path, $pos + 1);
		else
			$class_name = $class_path;
		
		if (file_exists(PROJECT_DIR . '/core/datasource/' . $class_path . '.class.php')) 
			$full_path = PROJECT_DIR . '/core/datasource/' . $class_path . '.class.php';
		elseif(file_exists(LIMB_DIR . '/core/datasource/' . $class_path . '.class.php'))
			$full_path = LIMB_DIR . '/core/datasource/' . $class_path . '.class.php';
		else
		{
			debug :: write_error('datasource not found', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('class_path' => $class_path));
			return null;
		}
			
		include_once($full_path);
	  $datasource =& new $class_name();
	  
	  return $datasource;
	}
}


?>