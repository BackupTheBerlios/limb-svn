<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: change_password_action.class.php 81 2004-03-26 13:51:05Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/actions/form_edit_site_object_action.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/required_rule.class.php');
require_once(LIMB_DIR . 'core/model/response/close_popup_response.class.php');

class change_user_locale_action extends form_action
{
	function change_user_locale_action()
	{
		parent :: form_action('change_locale_form');
	}
	
	function _init_validator()
	{
		$this->validator->add_rule(new required_rule('locale_id'));
	}
	
	function _valid_perform()
	{
		$locale_id = $this->dataspace->get('locale_id');

		if (!locale :: is_valid_locale_id($locale_id))
			return new close_popup_response(RESPONSE_STATUS_FAILURE);
		
		$user =& user :: instance();
		$user->set_locale_id($locale_id);
		
		return new close_popup_response(RESPONSE_STATUS_SUCCESS);
	}
}

?>