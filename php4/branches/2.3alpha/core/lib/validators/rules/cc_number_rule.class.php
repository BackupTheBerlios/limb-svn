<?php 
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: us_zip_rule.class.php 471 2004-08-03 14:09:36Z pachanga $
*
***********************************************************************************/ 
require_once(LIMB_DIR . '/core/lib/validators/rules/single_field_rule.class.php');

class cc_number_rule extends single_field_rule
{
	function check($value)
	{
	  $value = "$value";
	  $allowed_numbers = array(4, 5);
	  
	  if(in_array(substr($value, 0, 1), $allowed_numbers))
	  {
	    return;
	  }
	  else
	    $this->error(strings :: get('credit_card_number_error', 'error'));
	} 
} 
?>