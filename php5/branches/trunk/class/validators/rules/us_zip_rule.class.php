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
require_once(LIMB_DIR . 'class/validators/rules/single_field_rule.class.php');

class us_zip_rule extends single_field_rule
{
	protected function check($value)
	{
	  $value = "$value";
	  
	  if(strlen($value) == 5)
	  {
	    if(!preg_match("~^\d{5}$~", $value))
	      $this->error(strings :: get('error_invalid_zip_format', 'error'));
	    else
	      return;
	  }    
	  elseif(strlen($value) == 10)
	  {
	    if(!preg_match("~^\d{5}\s\d{4}$~", $value))
	      $this->error(strings :: get('error_invalid_zip_format', 'error'));
	    else
	      return;
	  }
	  else
	    $this->error(strings :: get('error_invalid_zip_format', 'error'));
	} 
} 
?>