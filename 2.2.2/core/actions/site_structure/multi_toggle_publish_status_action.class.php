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

class multi_toggle_publish_status_action extends form_action
{
	function _define_dataspace_name()
	{
	  return 'grid_form';
	}

	function _valid_perform(&$request, &$response)
	{
		if($request->has_attribute('popup'))
		  $response->write(close_popup_response($request));
	
		$data = $this->dataspace->export();
		
		if(!isset($data['ids']) || !is_array($data['ids']))
		{
  	  $request->set_status(REQUEST_STATUS_FAILURE);
  		return;			
		}
			
		$objects = $this->_get_objects(array_keys($data['ids']));

		foreach($objects as $id => $item)
		{
			if (!isset($item['actions']['publish']) || !isset($item['actions']['unpublish']))
				continue;
				
			$object = wrap_with_site_object($item);
			$status = $object->get_attribute('status');
			
			if ($status & SITE_OBJECT_PUBLISHED_STATUS)
			{
			
				$status &= ~SITE_OBJECT_PUBLISHED_STATUS;
				$action = 'unpublish';
			}	
			else
			{
				$status |= SITE_OBJECT_PUBLISHED_STATUS;
				$action = 'publish';
			}	

			$object->set_attribute('status', $status);
			$object->update(false);
			
			$this->_apply_access_policy($object, $action);
		}	

	  $request->set_status(REQUEST_STATUS_SUCCESS);
	}
	
	function _get_objects($node_ids)
	{
		$params = array(
			'restrict_by_class' => false
		);
		
		$objects =& fetch_by_node_ids($node_ids, 'site_object', $counter, $params);
		return $objects;
	}

	function _apply_access_policy($object, $action)
	{		
		$access_policy =& access_policy :: instance();
		
		if(!$access_policy->save_object_access_for_action($object, $action))
		{
			error('access template for action not defined',
				 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
				array(
					'action' => $action,
					'class_name' => get_class($object),
				)
			);
		}	
	}
}

?>