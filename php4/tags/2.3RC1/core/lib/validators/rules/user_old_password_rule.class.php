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
require_once(LIMB_DIR . '/core/lib/validators/rules/single_field_rule.class.php');
require_once(LIMB_DIR . '/core/model/site_object_factory.class.php');

class user_old_password_rule extends single_field_rule
{
  function user_old_password_rule($field_name)
  {
    parent :: single_field_rule($field_name);
  } 

  function validate(&$dataspace)
  {
    $old_password = $dataspace->get($this->field_name);
    
    $user_site_object =& site_object_factory :: create('user_object');
    
    if($user_site_object->validate_password($old_password))
      return;
    else  
      $this->error('WRONG_OLD_PASSWORD');
  } 
} 

?>