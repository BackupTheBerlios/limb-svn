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

/**
* For fields which must be supplied a value by the user
*/
class required_rule extends single_field_rule
{
	/**
	* Constructs required_rule
	* 
	* @param string $ field_name to validate
	* @access public 
	*/
	function required_rule($field_name)
	{
		parent :: single_field_rule($field_name);
	} 

	/**
	* Performs validation
	* 
	* @param dataspace $ - data to validate
	* @param error_list $ 
	* @return boolean TRUE if validation passed
	* @access public 
	*/
	function validate(&$dataspace)
	{
		$value = $dataspace->get($this->field_name);
		
		if (!isset($value) || $value === '')
		{
			$this->error(strings :: get('error_required', 'error'));
		} 
	} 
} 

?>