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
class locale_number_format_tag_info
{
	var $tag = 'locale:NUMBER_FORMAT';
	var $end_tag = ENDTAG_FORBIDDEN;
	var $tag_class = 'locale_number_format_tag';
} 

register_tag(new locale_number_format_tag_info());

class locale_number_format_tag extends server_component_tag
{
	var $runtime_component_path = '/class/template/components/locale_number_format_component';

	var $field;
	var $decimals;

	function pre_parse()
	{
		$field = $this->attributes['hash_id'];
		if (empty($field))
		{
			error('MISSINGREQUIREATTRIBUTE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'attribute' => 'hash_id',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 

		$this->field = $field;

		return PARSER_REQUIRE_PARSING;
	} 

	function generate_contents(&$code)
	{
		$code->write_php(
			'echo ' . $this->get_component_ref_code() . '->format(' . $this->get_dataspace_ref_code() . '->get("' . $this->field . '"));');
	}  
} 

?>