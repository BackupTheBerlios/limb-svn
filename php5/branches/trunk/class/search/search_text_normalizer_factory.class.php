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
require_once(LIMB_DIR . 'class/lib/system/objects_support.inc.php');

class search_text_normalizer_factory
{
  private function __construct(){}
  
	static public function create($class_name)
	{	
	  self :: _include_class_file($class_name);
	  
		return new $class_name();
	}

	static private function _include_class_file($class_name)
	{
	  if(class_exists($class_name))
	    return;
	    
		include_once(LIMB_DIR . '/class/search/normalizers/' . $class_name . '.class.php');
	}	
}
?>