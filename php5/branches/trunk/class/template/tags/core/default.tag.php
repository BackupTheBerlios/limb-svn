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
class core_default_tag_info
{
	public $tag = 'core:DEFAULT';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'core_default_tag';
} 

register_tag(new core_default_tag_info());

/**
* Allows a default action to take place at runtime, should a
* dataspace variable have failed to be populated
*/
class core_default_tag extends compiler_directive_tag
{
	public function pre_parse()
	{
		if (!isset($this->attributes['for']) || !$this->attributes['for'])
		{
			throw new WactException('missing required attribute', 
					array('tag' => $this->tag,
					'attribute' => 'for',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 

		return PARSER_REQUIRE_PARSING;
	} 

	public function pre_generate($code)
	{
		parent::pre_generate($code);
		$tempvar = $code->get_temp_variable();
		$code->write_php('$' . $tempvar . ' = trim(' . $this->get_dataspace_ref_code() . '->get(\'' . $this->attributes['for'] . '\'));');
		$code->write_php('if (empty($' . $tempvar . ')) {');
	} 

	public function post_generate($code)
	{
		$code->write_php('}');
		parent::post_generate($code);
	} 
} 

?>