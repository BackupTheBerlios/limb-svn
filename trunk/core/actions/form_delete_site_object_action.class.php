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
require_once(LIMB_DIR . 'core/actions/form_site_object_action.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/required_rule.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/tree_identifier_rule.class.php');
require_once(LIMB_DIR . 'core/model/response/close_popup_response.class.php');

class form_delete_site_object_action extends form_site_object_action
{
	function _define_dataspace_name()
	{
	  return 'delete_form';
	}

	function _valid_perform()
	{
		$object =& wrap_with_site_object(fetch_mapped_by_url());
		
		if(!$object->delete())
		{
			message_box :: write_notice('Can not be deleted!');
			return new failed_response();
		}

		return new close_popup_response(RESPONSE_STATUS_FORM_SUBMITTED, RELOAD_SELF_URL, true);
	}

}

?>
