<?php

class metadata_breadcrumbs_tag_info
{
	var $tag = 'metadata:BREADCRUMBS';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'metadata_breadcrumbs_tag';
} 

register_tag(new metadata_breadcrumbs_tag_info());

class metadata_breadcrumbs_tag extends server_component_tag
{
	var $runtime_component_path = '/core/template/components/metadata_component';
	
	function generate_constructor(&$code)
	{
		parent :: generate_constructor($code);
	} 
	
	function generate_contents(&$code)
	{
		$child_list =& $this->find_immediate_child_by_class('list_list_tag');
		
		if(isset($this->attributes['offset_path']))	
			$code->write_php($this->get_component_ref_code() . '->set_offset_path("' . $this->attributes['offset_path'] . '");');
			
		if ($child_list)
			$code->write_php($child_list->get_component_ref_code() . '->register_dataset(' . $this->get_component_ref_code() . '->get_breadcrumbs_dataset());');
		
		parent :: generate_contents($code);
	}
} 

?>