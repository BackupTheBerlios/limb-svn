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

class core_data_repeat_tag_info
{
	var $tag = 'core:REPEAT';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'core_data_repeat_tag';
} 

register_tag(new core_data_repeat_tag_info());

class core_data_repeat_tag extends compiler_directive_tag
{
	function generate_contents(&$code)
	{
		$dataspace = $this->get_dataspace_ref_code();
		
		$counter = '$' . $code->get_temp_variable();
		$value = '$' . $code->get_temp_variable();
		
		if (isset($this->attributes['hash_id']))
		{
			$code->write_php($value . ' = trim(' . $this->get_dataspace_ref_code() . '->get(\'' . $this->attributes['hash_id'] . '\'));');
		}
		else
		{
			if(!isset($this->attributes['value']))
				$this->attributes['value'] = 1;
				
			$code->write_php($value . ' = ' . $this->attributes['value'] . ';');
		}

		$code->write_php('for(' . $counter . '=0;' . $counter . ' < ' . $value . '; ' . $counter . '++){');		
		
		parent :: generate_contents($code);
		
		$code->write_php('}');
		
	} 
} 

?>