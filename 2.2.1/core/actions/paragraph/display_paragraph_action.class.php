<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: create_paragraph_action.class.php 202 2004-04-28 17:45:07Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/actions/action.class.php');

class display_paragraph_action extends action
{
	function perform(&$request, &$response)
	{
		$object_data = fetch_requested_object();
		
		$parent_data = fetch_one_by_node_id($object_data['parent_node_id']);
		$path = $parent_data['path'];
		
		$params = complex_array :: array_merge($_GET, $_POST);

		$sep = '';
		$query = '';
		
		$flat_params = array();
		complex_array :: to_flat_array($params, $flat_params);
		
		foreach ($flat_params as $key => $value)
		{
			$query .= $sep . $key . '=' . urlencode($value);
			$sep = '&';
		} 
		if (!empty($query))
			$path .= '?' . $query;
		
		$response->redirect($path);
	}
}

?>