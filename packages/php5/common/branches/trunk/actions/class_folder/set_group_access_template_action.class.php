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
require_once(LIMB_DIR . '/class/core/actions/form_action.class.php');

class set_group_access_template_action extends form_action
{
	protected function _define_dataspace_name()
	{
	  return 'set_group_access_template';
	}
	
	protected function _init_dataspace($request)
	{
		if (!$class_id = $request->get('class_id'))
		  throw new LimbException('class_id not defined');

		$access_policy =& access_policy :: instance();

		$data['template'] = $access_policy->get_group_action_access_templates($class_id);

		$this->dataspace->merge($data);
	}
	
	protected function _valid_perform($request, $response)
	{
		if (!$class_id = $request->get('class_id'))
		  throw new LimbException('class_id not defined');

		$data = $this->dataspace->export();
		$access_policy =& access_policy :: instance();

		$access_policy->save_group_action_access_template($class_id, $data['template']);

		$request->set_status(request :: STATUS_FORM_SUBMITTED);

		if($request->has_attribute('popup'))
			$response->write(close_popup_no_parent_reload_response());
	}
}
?>