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
require_once(LIMB_DIR . 'core/actions/form_action.class.php');
require_once(LIMB_DIR . 'core/model/response/close_popup_response.class.php');

class multi_delete_action extends form_action
{
	function multi_delete_action($name='grid_form')
	{		
		parent :: form_action($name);
	}
	
	function _init_dataspace()
	{
		parent :: _init_dataspace();
		
		$this->_transfer_dataspace();
	}
	
	function _first_time_perform()
	{
		$data = $this->dataspace->export();
		
		if(!isset($data['ids']) || !is_array($data['ids']))
			return new close_popup_response(RESPONSE_STATUS_FAILURE);
			
		$objects = $this->_get_objects_to_delete(array_keys($data['ids']));

		$grid =& $this->view->find_child('multi_delete');
		
		$grid->register_dataset(new array_dataset($objects));
	
		return parent :: _first_time_perform();
	}
	
	function _valid_perform()
	{
		$data = $this->dataspace->export();
		
		if(!isset($data['ids']) || !is_array($data['ids']))
			return new close_popup_response(RESPONSE_STATUS_FAILURE);
		
		$objects = $this->_get_objects_to_delete(array_keys($data['ids']));
		
		foreach($objects as $id => $item)
		{
			if($item['delete_status'] !== 0 )
				continue;
			
			$site_object =& wrap_with_site_object($item);
			
			if(!$site_object->delete())
			{
				debug :: write_error("object couldn't be deleted",
				 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
				array('node_id' => $id));

				return new close_popup_response(RESPONSE_STATUS_FAILURE);
			}
		}	
		return new close_popup_response();
	}
	
	function _get_objects_to_delete($node_ids)
	{
		$params = array(
			'restrict_by_class' => false
		);
		
		$objects =& fetch_by_node_ids($node_ids, 'site_object', $counter, $params);
		
		$result = array();
		$tree = tree :: instance();
		
		foreach($objects as $id => $item)
		{
			if (!isset($item['actions']['delete']))
			{
				$objects[$id]['delete_status'] = 1;
				$objects[$id]['delete_reason'] = strings :: get('delete_action_not_accessible', 'error');
				continue;
			}
			
			$site_object =& wrap_with_site_object($item);
			if (!$site_object->can_delete())
			{
				$objects[$id]['delete_status'] = 1;
				$objects[$id]['delete_reason'] = strings :: get('cant_be_deleted', 'error');
				continue;
			}	
			
			$objects[$id]['delete_reason'] = strings :: get('ok');
			$objects[$id]['delete_status'] = 0;
			$objects[$id]['ids'][$item['node_id']] = 1;
		}
		
		return $objects;
	}

}

?>