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
function & create_object($class_name, $include_dir='', $exact_dir=false)
{
	if($include_dir && !class_exists($class_name))
	{
		if($exact_dir)
			$dir = $include_dir;
		else
		{
			$dir = PROJECT_DIR . $include_dir;
			
			if(!file_exists($dir . $class_name . '.class.php'))
				$dir = LIMB_DIR . $include_dir;
		}
		
   	if(!file_exists($dir . $class_name . '.class.php'))
			error("class not found", 
				__FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
				array(
					'class_name' => $class_name,
					'include_dir' => $include_dir
				)
			);

		include_once($dir . $class_name . '.class.php');
	}
	
	$obj =& new $class_name();
	
	return $obj;
}

function & instantiate_object($class_name, $include_dir='', $exact_dir=false)
{
	if(	!isset($GLOBALS['global_'. $class_name]) || 
			get_class($GLOBALS['global_'. $class_name]) != $class_name)
	{  		
		$obj =& create_object($class_name, $include_dir, $exact_dir);
		
		$GLOBALS['global_' . $class_name] =& $obj;
	}
	else
		$obj =& $GLOBALS['global_' . $class_name];
	
	return $obj;
}

?>