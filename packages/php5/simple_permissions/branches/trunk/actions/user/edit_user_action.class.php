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
require_once(LIMB_DIR . 'class/core/actions/form_edit_site_object_action.class.php');

class edit_user_action extends form_edit_site_object_action
{
	protected function _define_site_object_class_name()
	{
	  return 'user_object';
	}

	protected function _define_dataspace_name()
	{
	  return 'edit_user';
	}

  protected function _define_datamap()
	{
	  return complex_array :: array_merge(
	      parent :: _define_datamap(),
	      array(
  				'name' => 'name',
  				'lastname' => 'lastname',
  				'email' => 'email',
	      )
	  );
	}

	protected function _define_increase_version_flag()
	{
	  return false;
	}

	protected function _init_validator()
	{
		parent :: _init_validator();
    
    $request = Limb :: toolkit()->getRequest();
    
		if ($object_data = Limb :: toolkit()->getFetcher()->fetch_requested_object($request))
		{
      $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/unique_user_rule', 'identifier', $object_data['identifier']));
      $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/unique_user_email_rule', 'email', $object_data['email']));
		}

    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'name'));
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'email'));
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/email_rule', 'email'));
	}
}

?>