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
require_once(LIMB_DIR . 'core/actions/form_edit_site_object_action.class.php');
require_once(LIMB_DIR . 'core/model/response/response.class.php');

class user_settings_action extends form_action
{
	function _define_dataspace_name()
	{
	  return 'user_settings_form';
	}
	
	function _init_dataspace()
	{
	
	}

	function _valid_perform()
	{
	
		return new response(RESPONSE_STATUS_FORM_SUBMITTED);
	}

	function _process_transfered_dataspace()
	{	
	
	}
}

?>