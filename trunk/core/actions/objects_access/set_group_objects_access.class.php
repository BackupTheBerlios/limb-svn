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
	function set_group_objects_access($name = 'set_group_access')
	{		
		parent :: form_action($name);
	}
	
	function _init_dataspace()
	{
		$access_policy =& access_policy :: instance();
		$data['policy'] = $access_policy->get_group_object_access();

		$this->_import($data);
	}
	
	function _valid_perform()
	{
		$data = $this->_export();
		
		$access_policy =& access_policy :: instance();

		$access_policy->save_group_object_access($data['policy']);
		
		return new response();
	}

}

?>