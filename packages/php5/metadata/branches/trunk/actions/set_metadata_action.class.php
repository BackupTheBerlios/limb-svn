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
require_once(dirname(__FILE__) . '/../metadata_manager.class.php');

class set_metadata_action extends form_action
{
	protected function _define_dataspace_name()
	{
	  return 'set_metadata';
	}

	protected function _init_dataspace($request)
	{
		$object_data = fetcher :: instance()->fetch_requested_object($request);

		$data = metadata_manager :: get_metadata($object_data['id']);
		$this->dataspace->import($data);
	}

	protected function _valid_perform($request, $response)
	{
		$object_data = fetcher :: instance()->fetch_requested_object($request);

		metadata_manager :: save_metadata($object_data['id'], 
		                                  $this->dataspace->get('keywords'), 
		                                  $this->dataspace->get('description'));

	  $request->set_status(request :: STATUS_FORM_SUBMITTED);
	}
}
?>