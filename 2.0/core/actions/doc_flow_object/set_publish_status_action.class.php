<?php

require_once(LIMB_DIR . 'core/actions/action.class.php');

class set_publish_status_action extends action
{
	function set_publish_status_action($name='')
	{
		parent :: action($name);
	}
	
	function set_publish_status($status)
	{
		$object_data = fetch_mapped_by_url();
		$object_data['status'] = $status;

		$object =& site_object_factory :: create($object_data['class_name']);

		$object->import_attributes($object_data);
		
		$access_policy =& access_policy :: instance();
		
		$site_object_controller =& $object->get_controller();
		$action = $site_object_controller->determine_action();
		
		if(!$access_policy->save_object_access_for_action($object, $action))
			return false;
		
		return $object->update(false, false);
	}
	
}

?>