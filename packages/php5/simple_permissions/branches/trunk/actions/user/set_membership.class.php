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

class set_membership extends form_action
{
	protected function _define_dataspace_name()
	{
	  return 'set_membership';
	}

	protected function _init_dataspace($request)
	{
		$object_data = Limb :: toolkit()->getFetcher()->fetch_requested_object($request);

		$object = Limb :: toolkit()->createSiteObject('user_object');

		$data['membership'] = $object->get_membership($object_data['id']);

		$this->dataspace->import($data);
	}

	protected function _valid_perform($request, $response)
	{
		$object_data = Limb :: toolkit()->getFetcher()->fetch_requested_object($request);

		$data = $this->dataspace->export();
		$object = Limb :: toolkit()->createSiteObject('user_object');

		$object->save_membership($object_data['id'], $data['membership']);

	  $request->set_status(request :: STATUS_FORM_SUBMITTED);
	}

}

?>