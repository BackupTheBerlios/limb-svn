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

class core_data_transfer_tag_info
{
	var $tag = 'core:DATA_TRANSFER';
	var $end_tag = ENDTAG_FORBIDDEN;
	var $tag_class = 'core_data_transfer_tag';
} 

register_tag(new core_data_transfer_tag_info());

class core_data_transfer_tag extends compiler_directive_tag
{
	function check_nesting_level()
	{
		if (!isset($this->attributes['target']))
		{
			error('ATTRIBUTE_REQUIRED', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'attribute' => 'target',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	}

	function generate_contents(&$code)
	{
		$dataspace = $this->get_dataspace_ref_code();
		
		if (isset($this->attributes['hash_id']) && isset($this->attributes['target']))
		{
			if($target =& $this->parent->find_child($this->attributes['target']))
			{			
				$code->write_php($target->get_component_ref_code() . '->register_dataset(new array_dataset(' . $dataspace . '->get("' . $this->attributes['hash_id'] . '")))');
			}
		}
		
		parent :: generate_contents($code);
	} 
} 

?>