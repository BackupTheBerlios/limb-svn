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
	public $tag = 'core:DATA_TRANSFER';
	public $end_tag = ENDTAG_FORBIDDEN;
	public $tag_class = 'core_data_transfer_tag';
} 

register_tag(new core_data_transfer_tag_info());

class core_data_transfer_tag extends compiler_directive_tag
{
	public function pre_parse()
	{
		if (!isset($this->attributes['target']))
		{
			throw new WactException('missing required attribute', 
					array('tag' => $this->tag,
					'attribute' => 'target',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 

    return PARSER_REQUIRE_PARSING;
	}

	public function generate_contents($code)
	{
		$dataspace = $this->get_dataspace_ref_code();
		
		if (isset($this->attributes['hash_id']) && isset($this->attributes['target']))
		{
			if($target = $this->parent->find_child($this->attributes['target']))
			{			
				$code->write_php($target->get_component_ref_code() . '->register_dataset(new array_dataset(' . $dataspace . '->get("' . $this->attributes['hash_id'] . '")))');
			}
		}
		
		parent :: generate_contents($code);
	} 
} 

?>