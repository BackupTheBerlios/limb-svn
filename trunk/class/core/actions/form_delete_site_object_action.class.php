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
require_once(LIMB_DIR . 'class/actions/form_site_object_action.class.php');

class form_delete_site_object_action extends form_site_object_action
{
	function _define_dataspace_name()
	{
	  return 'delete_form';
	}

	function _valid_perform(&$request, &$response)
	{
		$object =& wrap_with_site_object(fetch_requested_object());
		
		if(!$object->delete())
		{
			message_box :: write_notice(strings :: get('cant_be_deleted', 'error'));
			$request->set_status(REQUEST_STATUS_FAILURE);
			return;
		}
		$request->set_status(REQUEST_STATUS_FORM_SUBMITTED);
		
		if($request->has_attribute('popup'))
			$response->write(close_popup_response($request, RELOAD_SELF_URL, true));
	}

}

?>
