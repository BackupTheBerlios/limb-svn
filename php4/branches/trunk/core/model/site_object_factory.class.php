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
require_once(LIMB_DIR . 'core/lib/system/objects_support.inc.php');

class site_object_factory
{		
	function create($class_name)
	{	
		include_class($class_name, '/core/model/site_objects/');
  	return create_object($class_name);	
	}		
}
?>