<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: edit_chat_room_action.class.php 59 2004-03-22 13:54:41Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/actions/form_edit_site_object_action.class.php');
require_once(LIMB_DIR . 'core/model/response/close_popup_response.class.php');
require_once(LIMB_DIR . 'core/model/chat/chat_user.class.php');

class user_settings_action extends form_action
{
	function user_settings_action()
	{
		parent :: form_action('user_settings_form');
	}
	
	function _init_dataspace()
	{
		$chat_user_data = chat_user :: get_chat_user_data();
		
		$form_data['color'] = $chat_user_data['color'];
		$form_data['sex'] = $chat_user_data['status'];
		$this->_import($form_data);
	}

	function _valid_perform()
	{
		$chat_user_data = chat_user :: get_chat_user_data();
		$form_data = $this->dataspace->export();

		$chat_user_data['color'] = $form_data['color'];
		$chat_user_data['status'] = ($chat_user_data['status'] & (~3)) ^ $form_data['sex'];
		chat_user :: update_chat_user($chat_user_data);

		return new close_popup_response(RESPONSE_STATUS_FORM_SUBMITTED);
	}

	function _process_transfered_dataspace()
	{	
	
	}
}

?>