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
require_once(LIMB_DIR . 'class/core/actions/action.class.php');

class set_publish_status_action extends action
{
	public function perform($request, $response)
	{
		$request->set_status(request :: STATUS_SUCCESS);
		
		if($request->has_attribute('popup'))
			$response->write(close_popup_response($request));
	
		if(!$object = wrap_with_site_object(fetch_requested_object()))
  		return;
		
		$site_object_controller = $object->get_controller();
		$action = $site_object_controller->get_action($request);

		switch ($action)
		{
			case 'publish':
				$status = $this->get_publish_status($object);
			break;
			case 'unpublish':
				$status = $this->get_unpublish_status($object);
			break;
			default:
				return ;
			break;
		}
		
		$object->set('status', $status);
		$object->update(false);
		
		$this->_apply_access_policy($object, $action);

	  fetcher :: flush_cache();
	}
	
	public function get_publish_status($object)
	{
		$current_status = $object->get('status');
		$current_status |= site_object :: STATUS_PUBLISHED;
		return $current_status;
	}
	
	public function get_unpublish_status($object)
	{
		$current_status = $object->get('status');
		$current_status = $current_status & (~site_object :: STATUS_PUBLISHED);
		return $current_status;
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