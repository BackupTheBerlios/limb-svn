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
require_once(LIMB_DIR . 'class/core/actions/form_edit_site_object_action.class.php');
require_once(LIMB_DIR . 'class/validators/rules/required_rule.class.php');

class change_user_locale_action extends form_action
{
	protected function _define_dataspace_name()
	{
	  return 'change_locale_form';
	}
	
	protected function _init_validator()
	{
    $this->validator->add_rule($v = array(LIMB_DIR . 'class/validators/rules/required_rule', 'locale_id'));
	}
	
	protected function _valid_perform($request, $response)
	{
		$locale_id = $this->dataspace->get('locale_id');

		if($request->has_attribute('popup'))
		  $response->write(close_popup_response($request));
		elseif(isset($_SERVER['HTTP_REFERER']))
		  $response->redirect($_SERVER['HTTP_REFERER']);
		else
		  $response->redirect('/');

		if (!locale :: is_valid_locale_id($locale_id))
		{
		  $request->set_status(request :: STATUS_FAILURE);
		}
		
		user :: instance()->set_locale_id($locale_id);
		
		$request->set_status(request :: STATUS_SUCCESS);
	}
}

?>