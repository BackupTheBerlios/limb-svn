<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: class_data_source.class.php 415 2004-02-07 14:09:56Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/data_source/data_source.class.php');

class class_data_source extends data_source
{
	function class_data_source()
	{
		parent :: data_source();
	}

	function & get_data_set($params = array())
	{
		if(!isset($_REQUEST['class_id']))
			return new array_dataset();
		
		$class_id = $_REQUEST['class_id'];
		$db_table =& db_table_factory :: instance('sys_class');
		$class_data = $db_table->get_row_by_id($class_id);
		
		if ($class_data)
			return new array_dataset(array(0 => $class_data));
		else
			return new array_dataset(array());
	}		
}


?>
