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

function include_class($class_name, $include_dir='')
{
	if(class_exists($class_name))
		return;
	
	$dir = PROJECT_DIR . $include_dir;
		
	if(!file_exists($dir . $class_name . '.class.php'))
		$dir = LIMB_DIR . $include_dir;	
	
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

function & create_object($class_name, $arguments = array())
{
  $evaled_arguments = array(); 
  foreach($arguments as $key => $value) 
  { 
	  switch(gettype($value)) 
	  { 
	    case "object": 
	    	eval("\$" . $key . " =& \$arguments['" . $key . "'];"); 
	    break; 
	    case "string": 
	    	eval("\$" . $key . " = '" . $value . "';"); 
	    break;
	    default: 
	    	eval("\$" . $key . " = " . $value . ";"); 
	    break; 
	  } 
    $evaled_arguments[] = "$" . $key; 
  } 
  $evaled_arguments_str = implode(", ", $evaled_arguments);
  
  eval("\$obj =& new {$class_name}(" . $evaled_arguments_str . ");"); 
	
	return $obj;
}

function & instantiate_object($class_name, $arguments = array())
{
	if(	!isset($GLOBALS['global_'. $class_name]) || 
			get_class($GLOBALS['global_'. $class_name]) != $class_name)
	{  		
		$obj =& create_object($class_name, $arguments);
		
		$GLOBALS['global_' . $class_name] =& $obj;
	}
	else
		$obj =& $GLOBALS['global_' . $class_name];
	
	return $obj;
}

function & instantiate_session_object($class_name, $arguments = array())
{
	if(	!isset($_SESSION['global_'. $class_name]) || 
			get_class($_SESSION['global_'. $class_name]) != $class_name)
	{  		
		$obj =& create_object($class_name, $arguments);
		
		$_SESSION['global_' . $class_name] =& $obj;
		$_SESSION['session_classes_paths'][] = $obj->__get_class_path();
	}
	else
		$obj =& $_SESSION['global_' . $class_name];
	
	return $obj;
}
?>