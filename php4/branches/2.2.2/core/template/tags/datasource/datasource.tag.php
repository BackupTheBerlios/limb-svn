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
class datasource_tag_info
{
	var $tag = 'DATASOURCE';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'datasource_tag';
} 

register_tag(new datasource_tag_info());

class datasource_tag extends server_component_tag
{
	var $runtime_component_path = '/core/template/components/datasource_component';
	
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
		parent :: generate_contents($code);
		
		if(isset($this->attributes['navigator']))
		{
			$code->write_php($this->get_component_ref_code() . '->set("navigator_id", ' . $this->attributes['navigator'] .');');
			$code->write_php($this->get_component_ref_code() . '->setup_navigator();');
		}

		$code->write_php($this->get_component_ref_code() . '->set("target", ' . $this->attributes['target'] .');');
		$code->write_php($this->get_component_ref_code() . '->setup_target();');

		if(isset($this->attributes['navigator']))
		{
			$code->write_php($this->get_component_ref_code() . '->fill_navigator();');
		}
	} 	
} 

?>