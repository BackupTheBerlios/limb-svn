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

require_once(LIMB_DIR . '/tests/cases/site_objects/_content_object_template.test.php');

class test_user_manipulation extends test_content_object_template 
{  	
  function test_user_manipulation() 
  {
  	parent :: test_content_object_template();
  }

  function & _create_site_object()
  {
		$obj =& site_object_factory :: create('user_object');
  	return $obj;
  }
  
  function _set_object_initial_attributes()
  {
  	$this->object->set_attribute('name', 'user name');
  	$this->object->set_attribute('lastname', 'user last name');
  	$this->object->set_attribute('password', 'user password');
  	$this->object->set_attribute('email', 'user@here.com');
  	$this->object->set_attribute('generated_password', 'user generated password');
  }
	
	function _set_object_secondary_update_attributes()
	{
  	$this->object->set_attribute('name', 'user name2');
  	$this->object->set_attribute('lastname', 'user last name2');
  	$this->object->set_attribute('password', 'user password2');
  	$this->object->set_attribute('email', 'user@here.com2');
  	$this->object->set_attribute('generated_password', 'user generated password2');
	}

  
}

?>