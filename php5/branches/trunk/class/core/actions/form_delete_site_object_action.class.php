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
require_once(LIMB_DIR . 'class/core/actions/form_site_object_action.class.php');

class form_delete_site_object_action extends form_site_object_action
{
	protected function _define_dataspace_name()
	{
	  return 'delete_form';
	}

	protected function _valid_perform($request, $response)
	{
		$object = wrap_with_site_object(fetch_requested_object());
		
		try 
		{
		  $object->delete();
		}
		catch (SQLException $sql_e)
		{
		  throw $sql_e;
		}
		catch(LimbException $e)
		{
			message_box :: write_notice(strings :: get('cant_be_deleted', 'error'));
			$request->set_status(request :: STATUS_FAILURE);
			return;
		}
		
		$request->set_status(request :: STATUS_FORM_SUBMITTED);
		
		if($request->has_attribute('popup'))
			$response->write(close_popup_response($request, RELOAD_SELF_URL, true));
	}

}

?>
