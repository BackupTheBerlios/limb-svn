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

class set_group_objects_access extends form_action
{
	function _define_dataspace_name()
	{
	  return 'set_group_access';
	}
	
	function _init_dataspace()
	{
		$access_policy =& access_policy :: instance();
		$data['policy'] = $access_policy->get_group_object_access();

		$this->dataspace->import($data);
	}
	
	function _valid_perform()
	{
		$data = $this->dataspace->export();
		
		$access_policy =& access_policy :: instance();

		$access_policy->save_group_object_access($data['policy']);
		
		return new response(RESPONSE_STATUS_FORM_SUBMITTED);
	}

}

?>