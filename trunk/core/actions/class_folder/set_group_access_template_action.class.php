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
require_once(LIMB_DIR . 'core/model/response/close_popup_no_reload_response.class.php');

class set_group_access_template_action extends form_action
{
	function set_group_access_template_action($name='set_group_access_template')
	{		
		parent :: form_action($name);
	}
	
	function _init_dataspace()
	{
		if (!isset($_REQUEST['class_id']))
		{
			error('class_id not defined',
			 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
		}

		$access_policy =& access_policy :: instance();

		$data['template'] = $access_policy->get_group_action_access_templates($_REQUEST['class_id']);

		$this->_import($data);
	}
	
	function _valid_perform()
	{
		if (!isset($_REQUEST['class_id']))
		{
			error('class_id not defined',
			 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
		}

		$data = $this->_export();
		$access_policy =& access_policy :: instance();

		$access_policy->save_group_action_access_template($_REQUEST['class_id'], $data['template']);
		
		return new close_popup_no_reload_response(RESPONSE_STATUS_FORM_SUBMITTED);
	}
}
?>