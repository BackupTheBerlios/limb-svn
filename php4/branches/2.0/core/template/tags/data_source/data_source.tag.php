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
class data_source_tag_info
{
	var $tag = 'DATA_SOURCE';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'data_source_tag';
} 

register_tag(new data_source_tag_info());

class data_source_tag extends server_component_tag
{
	var $runtime_component_path = '/core/template/components/data_source_component';
	
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
		if(isset($this->attributes['navigator']))
		{
			if($navigator =& $this->parent->find_child($this->attributes['navigator']))
			{
				$limit = $code->get_temp_variable();
				$code->write_php('$' . $limit . '= ' . $navigator->get_component_ref_code() . '->get_items_per_page();');
				$code->write_php($this->get_component_ref_code() . '->set_parameter("limit", $' . $limit . ');');

				$code->write_php('if(isset($_GET["page_' . $navigator->get_server_id() . '"])){');
				$code->write_php($this->get_component_ref_code() . '->set_parameter("offset", ($_GET["page_' . $navigator->get_server_id() . '"]-1)*$' . $limit . ');');
				$code->write_php('}');
			}			
		}
		
		$target =& $this->parent->find_child($this->attributes['target']);
		
		parent :: generate_contents($code);
		
		if($target)
		{
			$code->write_php($target->get_component_ref_code() . '->register_dataset(' . $this->get_component_ref_code() . '->get_dataset());');
		}
			
		if(isset($this->attributes['navigator']) && $navigator)
		{
			$code->write_php($navigator->get_component_ref_code() . '->set_total_items(' . $this->get_component_ref_code() . '->get_total_count());');
		}
	} 	
} 

?>