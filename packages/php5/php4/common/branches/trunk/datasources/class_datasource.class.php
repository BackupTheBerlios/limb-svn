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
require_once(LIMB_DIR . 'class/datasources/datasource.class.php');

class class_datasource extends datasource
{
	function & get_dataset(&$counter, $params=array())
	{
		$counter = 0;
		
	  $request = request :: instance();
	  
		if(!$class_id = $request->get('class_id'))
			return new array_dataset();
			
		$db_table =& db_table_factory :: instance('sys_class');
		$class_data = $db_table->get_row_by_id($class_id);
		
		if ($class_data)
		{
			$counter = 1;
			return new array_dataset(array(0 => $class_data));
		}
		else
			return new array_dataset(array());
	}		
}


?>
