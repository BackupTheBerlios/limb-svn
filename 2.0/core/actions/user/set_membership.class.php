<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: set_membership.class.php 401 2004-02-04 15:40:14Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/actions/form_action.class.php');

class set_membership extends form_action
{
	function set_membership($name='set_membership')
	{		
		parent :: form_action($name);
	}
	
	function _init_dataspace()
	{
		$object_data =& fetch_mapped_by_url();
	
		$object =& site_object_factory :: create('user_object');
		
		$data['membership'] = $object->get_membership($object_data['id']);

		$this->_import($data);
	}
	
	function _valid_perform()
	{
		$object_data =& fetch_mapped_by_url();

		$data = $this->_export();
		$object =& site_object_factory :: create('user_object');
		
		$object->save_membership($object_data['id'], $data['membership']);
	
		return true;
	}

}

?>