<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . '/class/lib/system/objects_support.inc.php');

class search_text_normalizer_factory
{
  protected function __construct(){}
  
	static public function create($class_name)
	{	
	  self :: _include_class_file($class_name);
	  
		return new $class_name();
	}

	static protected function _include_class_file($class_name)
	{
	  if(class_exists($class_name))
	    return;
	    
		include_once(dirname(__FILE__) . '/' . $class_name . '.class.php');
	}	
}
?>