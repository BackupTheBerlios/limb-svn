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

class size_range_rule extends single_field_rule
{
	protected $min_len;
	protected $max_len;

	function __construct($field_name, $min_len, $max_len = null)
	{
		parent :: __construct($field_name);
		
		if (is_null($max_len))
		{
			$this->min_len = null;
			$this->max_len = $min_len;
		} 
		else
		{
			$this->min_len = $min_len;
			$this->max_len = $max_len;
		} 
	} 

	protected function check($value)
	{
		if (!is_null($this->min_len) && (strlen($value) < $this->min_len))
		{
			$this->error(strings :: get('size_too_small', 'error'));
		} 
		elseif (strlen($value) > $this->max_len)
		{
			$this->error(strings :: get('size_too_big', 'error'));
		} 
	} 
} 

?>