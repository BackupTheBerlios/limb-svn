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


class core_dataset_tag_info
{
	var $tag = 'core:DATASET';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'core_dataset_tag';
} 

register_tag(new core_dataset_tag_info());

class core_dataset_tag extends compiler_directive_tag
{
	
	function generate_contents(&$code)
	{
		$dataspace = $this->get_dataspace_ref_code();
		
		if (isset($this->attributes['hash_id']) && isset($this->attributes['child_id']))
		{
			if($child =& $this->find_child($this->attributes['child_id']))
			{			
				$code->write_php($child->get_component_ref_code() . '->register_dataset(new array_dataset(' . $dataspace . '->get("' . $this->attributes['hash_id'] . '")))');
			}
		}
		
		parent :: generate_contents($code);
	} 
} 

?>