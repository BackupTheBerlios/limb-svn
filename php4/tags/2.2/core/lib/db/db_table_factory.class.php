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

class db_table_factory
{		
	function create($db_table_name)
	{	
		include_class($db_table_name . '_db_table', '/core/db_tables/');
  	return create_object($db_table_name . '_db_table');	
	}
	
	function & instance($db_table_name)
	{	
		include_class($db_table_name . '_db_table', '/core/db_tables/');
		$obj =&	instantiate_object($db_table_name . '_db_table');
		return $obj;
	}

}
?>