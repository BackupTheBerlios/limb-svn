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
require_once(LIMB_DIR . '/class/datasources/fetch_datasource.class.php');

class last_objects_datasource extends fetch_datasource
{
	protected function _fetch(&$counter, $params)
	{
		$result = parent :: _fetch($counter, $params);

		if (!count($result))
			return $result;

		$this->_process_loaded_items($result);

		return $result;
	}

	protected function _process_loaded_items(&$items)
	{
		if (!count($items))
			return $items;

		$parent_node_ids = array();

		foreach($items as $key => $data)
			if (!isset($parent_node_ids[$data['parent_node_id']]))
			{
				$parent_node_ids[$data['parent_node_id']] = $data['parent_node_id'];
			}

    $datasource = Limb :: toolkit()->getDatasource('single_objects_by_node_ids_datasource');
    $datasource->set_use_node_ids_as_keys();
    $datasource->set_node_ids($parent_node_ids);

		$parents = $datasource->fetch();

		foreach($items as $key => $data)
		{
			$parent_data = $parents[$data['parent_node_id']];
			$items[$key]['parent_title'] = $parent_data['title'];
			$items[$key]['parent_path'] = $parent_data['path'];
		}
	}
}


?>