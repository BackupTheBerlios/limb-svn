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

class change_own_password_action extends form_action
{
	protected function _define_dataspace_name()
	{
	  return 'change_own_password';
	}

	protected function _init_validator()
	{
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/user_old_password_rule', 'old_password'));
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'password'));
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'second_password'));
    $this->validator->add_rule( array(LIMB_DIR . 'class/validators/rules/match_rule', 'second_password', 'password', 'PASSWORD'));
	}

	protected function _valid_perform($request, $response)
	{
		$user_object = site_object_factory :: create('user_object');
		
		$data = $this->dataspace->export();
    
    try 
    {
      $user_object->change_own_password($data['password']);
    }
    catch(SQLException $e)
    {
      throw $e;
    }
    catch(LimbException $e)
    {
      $request->set_status(request :: STATUS_FAILED);
    }
    
    $request->set_status(request :: STATUS_FORM_SUBMITTED);

		user :: instance()->logout();
		message_box :: write_warning(strings :: get('need_relogin', 'user'));
	}
}

?>