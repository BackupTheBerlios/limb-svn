<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: set_group_access_template_action.class.php 416 2004-02-07 14:16:14Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/actions/form_action.class.php');

class set_group_access_template_action extends form_action
{
	function set_group_access_template_action($name='set_group_access_template')
	{		
		parent :: form_action($name);
	}
	
	function _init_dataspace()
	{
		$object_data =& fetch_mapped_by_url();

		if (!isset($_REQUEST['class_id']))
		{
			close_popup($object_data['path']);
		}

		$access_policy =& access_policy :: instance();

		$data['template'] = $access_policy->get_group_action_access_templates($_REQUEST['class_id']);

		$this->_import($data);
	}
	
	function _valid_perform()
	{
		if (!isset($_REQUEST['class_id']))
			return false;

		$data = $this->_export();
		$access_policy =& access_policy :: instance();

		$access_policy->save_group_action_access_template($_REQUEST['class_id'], $data['template']);
		
		close_popup();
	}
}
?>