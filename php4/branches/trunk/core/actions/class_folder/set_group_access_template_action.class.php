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

class set_group_access_template_action extends form_action
{
	function _define_dataspace_name()
	{
	  return 'set_group_access_template';
	}
	
	function _init_dataspace(&$request)
	{
		if (!$controller_id = $request->get_attribute('controller_id'))
		{
			error('controller_id not defined',
			 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
		}

		$access_policy =& access_policy :: instance();

		$data['template'] = $access_policy->get_group_action_access_templates($controller_id);

		$this->dataspace->import($data);
	}
	
	function _valid_perform(&$request, &$response)
	{
		if (!$controller_id = $request->get_attribute('controller_id'))
		{
			error('controller_id not defined',
			 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
		}

		$data = $this->dataspace->export();
		$access_policy =& access_policy :: instance();

		$access_policy->save_group_action_access_template($controller_id, $data['template']);

		$request->set_status(REQUEST_STATUS_FORM_SUBMITTED);

		if($request->has_attribute('popup'))
			$response->write(close_popup_no_parent_reload_response());
	}
}
?>