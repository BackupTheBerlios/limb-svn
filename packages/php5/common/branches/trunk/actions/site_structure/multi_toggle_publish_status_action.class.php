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
require_once(LIMB_DIR . 'class/core/actions/form_action.class.php');

class multi_toggle_publish_status_action extends form_action
{
	protected function _define_dataspace_name()
	{
	  return 'grid_form';
	}

	protected function _valid_perform($request, $response)
	{
		if($request->has_attribute('popup'))
		  $response->write(close_popup_response($request));
	
		$data = $this->dataspace->export();
		
		if(!isset($data['ids']) || !is_array($data['ids']))
		{
  	  $request->set_status(request :: STATUS_FAILURE);
  		return;			
		}
			
		$objects = $this->_get_objects(array_keys($data['ids']));

		foreach($objects as $id => $item)
		{
			if (!isset($item['actions']['publish']) || !isset($item['actions']['unpublish']))
				continue;
				
			$object = wrap_with_site_object($item);
			$status = $object->get('status');
			
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

			$object->set('status', $status);
			$object->update(false);
			
			$this->_apply_access_policy($object, $action);
		}	

	  $request->set_status(request :: STATUS_SUCCESS);
	}
	
	protected function _get_objects($node_ids)
	{
		$params = array(
			'restrict_by_class' => false
		);
		
		return fetch_by_node_ids($node_ids, 'site_object', $counter, $params);
	}

	protected function _apply_access_policy($object, $action)
	{		
	  try
	  {
	    access_policy :: instance()->save_object_access_for_action($object, $action)
	  }
	  catch(LimbException $e)
	  {
	    message_box :: write_notice("Access template of " . get_class($object) . " for action '{$action}' not defined!!!");
	  }
	}
}

?>