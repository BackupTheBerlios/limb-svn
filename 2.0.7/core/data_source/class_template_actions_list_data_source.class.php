<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: class_template_actions_list_data_source.class.php 415 2004-02-07 14:09:56Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/data_source/data_source.class.php');

class class_template_actions_list_data_source extends data_source
{
	function class_template_actions_list_data_source()
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
		
		if (!$class_data)
			return new array_dataset();

		$c =& site_object_factory :: instance($class_data['class_name']);	
		
		$site_object_controller =& $c->get_controller();
		
		$actions = $site_object_controller->get_actions_definitions();
		
		$result = array();
		foreach($actions as $action => $action_params)
		{
			if (!isset($action_params['can_have_access_template']) || !$action_params['can_have_access_template'])
				continue;

			if(isset($action_params['action_name']))
				$result[$action]['action_name'] = $action_params['action_name'];
			else
				$result[$action]['action_name'] = str_replace('_', ' ', strtoupper($action{0}) . substr($action, 1));				
		}
		
		return new array_dataset($result);
	}
}


?>