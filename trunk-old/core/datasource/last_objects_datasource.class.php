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
require_once(LIMB_DIR . 'core/datasource/fetch_datasource.class.php');

class last_objects_datasource extends fetch_datasource
{
	function last_objects_datasource()
	{
		parent :: fetch_datasource();
	}

	function & _fetch(&$counter, $params)
	{
		$result =& parent :: _fetch($counter, $params);
		
		if (!count($result))
			return $result;
			
		$parent_node_ids = array();

		foreach($result as $key => $data)
			if (!isset($parent_node_ids[$data['parent_node_id']]))
			{
				$parent_node_ids[$data['parent_node_id']] = $data['parent_node_id'];
			}	

		$fetcher =& fetcher :: instance();
		$access_policy =& access_policy :: instance();
		
		$params = array(
			'restrict_by_class' => false
		);
		
		$parents =& $fetcher->fetch_by_node_ids($parent_node_ids, 'site_object', $parents_counter, $params, 'fetch_by_ids');
		
		foreach($result as $key => $data)
		{
			$parent_data = $parents[$data['parent_node_id']];
			$result[$key]['parent_title'] = $parent_data['title'];
			$result[$key]['parent_path'] = $parent_data['path'];
		}	

		return $result;
	}
}


?>