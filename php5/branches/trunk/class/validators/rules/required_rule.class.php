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
require_once(LIMB_DIR . '/class/validators/rules/single_field_rule.class.php');

class required_rule extends single_field_rule
{
	public function validate($dataspace)
	{
		$value = $dataspace->get($this->field_name);
		
		if (!isset($value) || $value === '')
		{
			$this->error(strings :: get('error_required', 'error'));
		} 
	} 
} 

?>