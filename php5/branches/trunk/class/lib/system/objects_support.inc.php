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

function instantiate_session_object($class_name, &$arguments = array())
{
	if(	!isset($_SESSION['global_session_singleton_'. $class_name]) || 
			get_class($_SESSION['global_session_singleton_'. $class_name]) != $class_name)
	{ 
    $handle =& $arguments;
    array_unshift($handle, $class_name);
    
    resolve_handle($handle);
    
		$_SESSION['global_session_singleton_' . $class_name] = $handle;
	}
	else
		$handle = $_SESSION['global_session_singleton_' . $class_name];
	
	return $handle;
}

//Original idea by Jeff Moore, http://wact.sourceforge.net/index.php/ResolveHandle
function resolve_handle(&$handle)
{
  if (is_object($handle) || is_null($handle))
    return;
  
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
      throw new Exception('too many arguments for resolve handle');
  }
}

?>