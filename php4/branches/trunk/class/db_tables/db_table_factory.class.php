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

class db_table_factory
{		
	function create($db_table_name)
	{	
	  db_table_factory :: _include_class_file($db_table_name);

  	return create_object($db_table_name . '_db_table');	
	}
	
	function & instance($db_table_name)
	{	
	  db_table_factory :: _include_class_file($db_table_name);

		$obj =&	instantiate_object($db_table_name . '_db_table');
		return $obj;
	}

	function _include_class_file($db_table_name)
	{
	  if(class_exists($db_table_name . '_db_table'))
	    return;
	  
		$resolver =& get_file_resolver('db_table');
		resolve_handle($resolver);
		
		$full_path = $resolver->resolve($db_table_name);

		include_once($full_path);
	}	

}
?>