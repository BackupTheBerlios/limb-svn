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
require_once(LIMB_DIR . '/class/datasources/datasource.interface.php');

class class_list_datasource implements datasource
{
	public function get_dataset(&$counter, $params = array())
	{
    $request = Limb :: toolkit()->getRequest();
    
    $datasource = Limb :: toolkit()->createDatasource('requested_object_datasource');
    $datasource->set_request($request);
    
		if(!$arr = $datasource->fetch())
			return new array_dataset();

		$db_table = Limb :: toolkit()->createDBTable('sys_class');
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

		$counter = sizeof($result);
		return new array_dataset($result);
	}
}


?>