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
class grid_separator_tag_info
{
	var $tag = 'grid:SEPARATOR';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'grid_separator_tag';
} 

register_tag(new grid_separator_tag_info());

class grid_separator_tag extends compiler_directive_tag
{
	var $count;

	function pre_parse()
	{
		$count = $this->attributes['count'];
		if (empty($count))
		{
			error('MISSINGREQUIREATTRIBUTE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'attribute' => 'count',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 

		$this->count = $count;

		return PARSER_REQUIRE_PARSING;
	} 

	function pre_generate(&$code)
	{
		parent::pre_generate($code);

		$counter = $code->get_temp_variable();
		
		$code->write_php('$' . $counter . ' = trim(' . $this->get_dataspace_ref_code() . '->get_counter());');
	
		$code->write_php('if (($' . $counter .') && ($' . $counter . '%' . $this->count . ' == 0)) {');
	} 

	function post_generate(&$code)
	{
		$code->write_php('}');
		parent::post_generate($code);
	} 
} 

?>