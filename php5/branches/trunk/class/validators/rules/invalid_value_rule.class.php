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

class invalid_value_rule extends single_field_rule
{
  protected $invalid_value;
  
	function __construct($field_name, $invalid_value)
	{
		parent :: __construct($field_name);
		
		$this->invalid_value = $invalid_value;
	} 

	public function validate($dataspace)
	{
		$value = $dataspace->get($this->field_name);
		
		$invalid_value = $this->invalid_value;
		
		settype($invalid_value, 'string');//???
		
		if ($value == $invalid_value)
		{
			$this->error(strings :: get('error_invalid_value', 'error'));
		} 
	} 
} 

?>