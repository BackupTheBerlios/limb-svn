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
require_once(LIMB_DIR . '/class/core/actions/form_action.class.php');
require_once(dirname(__FILE__) . '/../../access_policy.class.php');

class set_group_access extends form_action
{
	protected function _define_dataspace_name()
	{
	  return 'set_group_access';
	}
   
	protected function _init_dataspace($request)
	{
		if (!$class_id = $request->get('class_id'))
		  throw new LimbException('class_id not defined');

    $access_policy = new access_policy();
		$policy = $access_policy->get_actions_access($class_id, access_policy :: ACCESSOR_TYPE_GROUP);

		$this->dataspace->set('policy', $policy);
	}
	
	protected function _valid_perform($request, $response)
	{
		if (!$class_id = $request->get('class_id'))
		  throw new LimbException('class_id not defined');
		
    $access_policy = new access_policy();
		$access_policy->save_actions_access($class_id, $this->dataspace->get('policy'), access_policy :: ACCESSOR_TYPE_GROUP);

		$request->set_status(request :: STATUS_FORM_SUBMITTED);

		if($request->has_attribute('popup'))
			$response->write(close_popup_no_parent_reload_response());
	}

}

?>