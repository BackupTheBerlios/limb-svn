<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: login_command.class.php 827 2004-10-23 15:00:44Z seregalimb $
*
***********************************************************************************/ 
require_once(LIMB_DIR . '/class/core/commands/form_command.class.php');

class login_form_command extends form_command
{
	protected function _register_validation_rules($validator, $dataspace)
	{
		$validator->add_rule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'login'));
		$validator->add_rule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'password'));
	}
	
	protected function _merge_dataspace_with_request($dataspace, $request)
	{
		parent :: _merge_dataspace_with_request($dataspace, $request);
		
		$this->_transfer_redirect_param($dataspace, $request);
	}	
	
	protected function _transfer_redirect_param($dataspace, $request)
	{
		if(!$redirect = $request->get('redirect'))
			return;

		$dataspace->set('redirect', urldecode($this->_get_redirect_string($request)));
	}
	
	protected function _get_redirect_string($request)
	{
		if(!$redirect = $request->get('redirect'))
			return '';
			
		if(!preg_match("/^([a-z0-9\.#\/\?&=\+\-_]+)/si", $redirect))
			return '';
				
		return $redirect;
	}
}

?>