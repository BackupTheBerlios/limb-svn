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
require_once(LIMB_DIR . 'core/actions/action.class.php');
require_once(LIMB_DIR . 'core/model/response/close_popup_response.class.php');

class set_publish_status_action extends action
{
	function set_publish_status_action($name='')
	{
		parent :: action($name);
	}
	
	function perform()
	{
		if(!$object = wrap_with_site_object(fetch_mapped_by_url()))
			return new close_popup_response();
		
		$site_object_controller =& $object->get_controller();
		$action = $site_object_controller->determine_action();

		switch ($action)
		{
			case 'publish':
				$status = $this->get_publish_status($object);
			break;
			case 'unpublish':
				$status = $this->get_unpublish_status($object);
			break;
			default:
				return new close_popup_response();
			break;
		}

		$object->set_attribute('status', $status);
		$object->update(false);

		$this->_apply_access_policy($object, $action);
			
		return new close_popup_response();
	}
	
	function get_publish_status($object)
	{
		$current_status = $object->get_attribute('status');
		$current_status |= SITE_OBJECT_PUBLISHED_STATUS;
		return $current_status;
	}
	
	function get_unpublish_status($object)
	{
		$current_status = $object->get_attribute('status');
		$current_status &= !(SITE_OBJECT_PUBLISHED_STATUS);
		return $current_status;
	}
		
	function _apply_access_policy($object, $action)
	{		
		$access_policy =& access_policy :: instance();
		
		if(!$access_policy->save_object_access_for_action($object, $action))
		{
			error('access template for action not defined',
				 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
				array('action' => $action));
		}	
	}
}

?>