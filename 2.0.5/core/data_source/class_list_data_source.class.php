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
require_once(LIMB_DIR . 'core/data_source/data_source.class.php');

class class_list_data_source extends data_source
{
	function class_list_data_source()
	{
		parent :: data_source();
	}

	function & get_data_set($params = array())
	{
		if(!$arr = fetch_mapped_by_url())
			return new array_dataset();
		
		$db_table =& db_table_factory :: instance('sys_class');
		$classes = $db_table->get_list('', 'class_name');
		
		$result = array();
		$params = array();

		foreach($classes as $class_id => $class_data)
		{
			$result[$class_id] = $class_data;
			$result[$class_id]['path'] = $arr['path'];
			$params['class_id'] = $class_id;
			$result[$class_id]['node_id'] = $arr['node_id'];
			
			foreach($arr['actions'] as $action_name => $action_params)
				$arr['actions'][$action_name]['extra'] = $params;

			$result[$class_id]['actions'] = $arr['actions'];
		}
		
		return new array_dataset($result);
	}
}


?>