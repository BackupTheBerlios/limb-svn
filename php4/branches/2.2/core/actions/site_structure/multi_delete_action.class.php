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

class multi_delete_action extends form_action
{
	function _define_dataspace_name()
	{
	  return 'grid_form';
	}

	function _init_dataspace(&$request)
	{
		parent :: _init_dataspace($request);
		
		$this->_transfer_dataspace($request);
	}
	
	function _first_time_perform(&$request, &$response)
	{
		$data = $this->dataspace->export();
		
		if(!isset($data['ids']) || !is_array($data['ids']))
		{
		  $request->set_status(REQUEST_STATUS_FAILURE);
		  		  
			if($request->has_attribute('popup'))
			  $response->write(close_popup_response($request));
			  
			return;
		}
			
		$objects = $this->_get_objects_to_delete(array_keys($data['ids']));

		$grid =& $this->view->find_child('multi_delete');
		
		$grid->register_dataset(new array_dataset($objects));
	
		parent :: _first_time_perform(&$request, &$response);
	}
	
	function _valid_perform(&$request, &$response)
	{
		$data = $this->dataspace->export();

	  $request->set_status(REQUEST_STATUS_FAILURE);
	  		  
		if($request->has_attribute('popup'))
		  $response->write(close_popup_response($request));
		
		if(!isset($data['ids']) || !is_array($data['ids']))
			return;
		
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

  			return;
			}
		}	
	  $request->set_status(REQUEST_STATUS_SUCCESS);
	
		$response->write(close_popup_response($request));
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