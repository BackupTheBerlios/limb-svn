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

class edit_poll_action extends form_edit_site_object_action
{
	protected function _define_site_object_class_name()
	{
	  return 'poll';
	}  
	  
	protected function _define_dataspace_name()
	{
	  return 'edit_poll';
	}
  
  protected function _define_datamap()
	{
	  return complex_array :: array_merge(
	      parent :: _define_datamap(),
	      array(
  				'start_date' => 'start_date',
  				'finish_date' => 'finish_date',
  				'restriction' => 'restriction',
	      )
	  );     
	}   
	
	protected function _init_validator()
	{
		parent :: _init_validator();

    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'start_date'));
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'finish_date'));
    $this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'restriction'));
	}
}

?>