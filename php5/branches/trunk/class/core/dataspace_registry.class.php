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
require_once(LIMB_DIR . '/class/core/dataspace.class.php');

class dataspace_registry
{
	function & get($name)
	{
		$obj = null;
  	
  	$instance_name = "global_dataspace_instance_{$name}";
  	
  	if(isset($GLOBALS[$instance_name]))
			$obj =& $GLOBALS[$instance_name];
		
  	if(!$obj || get_class($obj) != 'dataspace')
  	{
  		$obj = new dataspace();
  		$GLOBALS[$instance_name] =& $obj;
  	}
  	
  	return $obj;
	}
}

?>