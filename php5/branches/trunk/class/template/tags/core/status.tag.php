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
class core_status_tag_info
{
	public $tag = 'core:STATUS';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'core_status_tag';
} 

register_tag(new core_status_tag_info());

/**
* Defines an action take, should a dataspace variable have been set at runtime.
* The opposite of the core_default_tag
*/
class core_status_tag extends compiler_directive_tag
{
	protected $const;

	public function pre_parse()
	{
		if (!isset($this->attributes['name']))
		{
			error('MISSINGREQUIREATTRIBUTE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'attribute' => 'name',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
		
		$this->const = $this->attributes['name'];

		return PARSER_REQUIRE_PARSING;
	} 

	public function pre_generate($code)
	{
		parent::pre_generate($code);
		
		$value = 'true';
		if (isset($this->attributes['value']) && !(boolean)$this->attributes['value'])
			$value = 'false';

		$tempvar = $code->get_temp_variable();
		$code->write_php('$' . $tempvar . ' = trim(' . $this->get_dataspace_ref_code() . '->get("status"));');
		$code->write_php('if ((boolean)(constant("' . $this->const . '") & $' . $tempvar . ') === ' . $value . ') {');
	} 

	public function post_generate($code)
	{
		$code->write_php('}');
		parent::post_generate($code);
	} 
} 

?>