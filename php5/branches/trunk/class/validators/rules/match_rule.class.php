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

class match_rule extends single_field_rule
{
	protected $match_field;

	protected $match_field_name;

	function __construct($field_name, $match_field, $match_field_name = '')
	{
		$this->match_field = $match_field;
		if (!$match_field_name)
			$this->match_field_name = $match_field;
		else	
			$this->match_field_name = $match_field_name;
			
		parent :: __construct($field_name);
	}

	public function validate($dataspace)
	{
		$value1 = $dataspace->get($this->field_name);
		$value2 = $dataspace->get($this->match_field);

		if (isset($value1) && isset($value2))
		{
			if (strcmp($value1, $value2))
			{
				$this->error(strings :: get('error_no_match', 'error'), array('match_field' => $this->match_field_name));
			}
		}
	}
}
?>