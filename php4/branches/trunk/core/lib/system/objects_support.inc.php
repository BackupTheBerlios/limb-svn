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
  $handle =& $arguments;
  array_unshift($handle, $class_name);
  
  resolve_handle($handle);
  
  return $handle;
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
	}
	else
		$obj =& $_SESSION['global_' . $class_name];
	
	return $obj;
}

function resolve_handle(&$handle)
{
  if (!is_object($handle) && !is_null($handle)) 
  {
      if (is_array($handle)) 
      {
        $class = array_shift($handle);
        $construction_args = $handle;
      } 
      else 
      {
        $construction_args = array();
        $class = $handle;
      }
      
      if (is_integer($pos = strpos($class, '|')))
      {
        $file = substr($class, 0, $pos);
        $class = substr($class, $pos + 1);
        include_once($file);
      }
      elseif(is_integer($pos = strrpos($class, '/')))
      {
        $file = $class;
      	$class = substr($class, $pos + 1);
        include_once($file . '.class.php');	
      }
      
      switch (count($construction_args)) 
      {
        case 0:
          $handle = new $class();
          break;
        case 1:
          $handle = new $class(array_shift($construction_args));
          break;
        case 2:
          $handle = new $class(
              array_shift($construction_args), 
              array_shift($construction_args));
          break;
        case 3:
          $handle = new $class(
              array_shift($construction_args), 
              array_shift($construction_args), 
              array_shift($construction_args));
          break;
        default:
          // Too many arguments for this cobbled together implemenentation.  :(
          die();
      }
   }

}

?>