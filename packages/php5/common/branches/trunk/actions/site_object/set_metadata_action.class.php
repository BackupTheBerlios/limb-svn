<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'class/core/actions/form_action.class.php');

class set_metadata_action extends form_action
{
	protected function _define_dataspace_name()
	{
	  return 'set_metadata';
	}
	
	protected function _init_dataspace($request)
	{
		$object_data = fetch_requested_object($request);

		$object = site_object_factory :: create('site_object');
		$object->set('id', $object_data['id']);
		
		$data = $object->get_metadata();
		$this->dataspace->import($data);
	}

	protected function _valid_perform($request, $response)
	{
		$object_data = fetch_requested_object($request);

		$data = $this->dataspace->export();

		$data['id'] = $object_data['id'];
		
		$object = site_object_factory :: create('site_object');
		$object->merge($data);
		
		if(!$object->save_metadata())
			$request->set_status(request :: STATUS_FAILURE);
		else
		  $request->set_status(request :: STATUS_FORM_SUBMITTED);
	}
}
?>