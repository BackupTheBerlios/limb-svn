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
require_once(LIMB_DIR . 'core/actions/tree_toggle_action.class.php');

class group_objects_access_tree_toggle_action extends tree_toggle_action
{
	function group_objects_access_tree_toggle_action($name='set_group_access')
	{
		parent :: tree_toggle_action($name);
	}
	
	function perform()
	{
		if(!parent :: perform())
			return false;
				
		$access_policy =& access_policy :: instance();
	
		$data['policy'] = $access_policy->get_group_object_access();

		$this->_import($data);
		
		return true;
	}
}

?>