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
class pop3_factory
{
	function pop3_factory()
	{
	}
		
	function & create($library = '',$dir = '')
	{
		$object_class_name = 'pop3_'. $library;
		
		if(isset($GLOBALS['global_'. $object_class_name]))
			$obj =& $GLOBALS['global_'. $object_class_name];
		else
			$obj = null;

  	if(get_class($obj) != $object_class_name)
  	{
		  $dir = ($dir == '') ? PHP_MAIL_DIR_C : $dir;
		  include_once($dir.$object_class_name.'.class.php');
		  $obj =& new $object_class_name();
  		$GLOBALS['global_'. $object_class_name] =& $obj;
  	}
  	
  	return $obj;
	}

}
?>