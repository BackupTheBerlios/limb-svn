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
require_once(LIMB_DIR . '/core/actions/form_action.class.php');

class set_membership extends form_action
{
	function _define_dataspace_name()
	{
	  return 'set_membership';
	}
	
	function _init_dataspace(&$request)
	{
		$object_data =& fetch_requested_object($request);
	
		$object =& site_object_factory :: create('user_object');
		
		$data['membership'] = $object->get_membership($object_data['id']);

		$this->dataspace->import($data);
	}
	
	function _valid_perform(&$request, &$response)
	{
		$object_data =& fetch_requested_object($request);

		$data = $this->dataspace->export();
		$object =& site_object_factory :: create('user_object');
		
		$object->save_membership($object_data['id'], $data['membership']);

	  $request->set_status(REQUEST_STATUS_FORM_SUBMITTED);
	}

}

?>