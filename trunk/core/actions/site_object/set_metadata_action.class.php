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
require_once(LIMB_DIR . 'core/lib/util/complex_array.class.php');
require_once(LIMB_DIR . 'core/actions/form_action.class.php');

class set_metadata_action extends form_action
{
	function set_metadata_action($name='set_metadata')
	{
		parent :: form_action($name);
	}
	
	function _init_dataspace()
	{
		$object_data =& fetch_mapped_by_url();

		$object =& site_object_factory :: create('site_object');
		$object->set_attribute('id', $object_data['id']);
		
		$data = $object->get_metadata();
		$this->_import($data);
	}

	function _valid_perform()
	{
		$object_data =& fetch_mapped_by_url();

		$data = $this->dataspace->export();

		$data['id'] = $object_data['id'];
		
		$object =& site_object_factory :: create('content_object');
		$object->import_attributes($data);
		
		if(!$object->save_metadata())
			return new failed_response();
		
		return new response();
	}
}
?>