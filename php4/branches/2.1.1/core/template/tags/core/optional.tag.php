<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: optional.tag.php 440 2004-02-12 16:56:15Z mike $
*
***********************************************************************************/ 
class core_attributeal_tag_info
{
	var $tag = 'core:OPTIONAL';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'core_attributeal_tag';
} 

register_tag(new core_attributeal_tag_info());

/**
* Defines an action take, should a dataspace variable have been set at runtime.
* The opposite of the core_default_tag
*/
class core_attributeal_tag extends compiler_directive_tag
{
	/**
	* The dataspace variable name
	* 
	* @var string 
	* @access private 
	*/
	var $field;

	/**
	* 
	* @return int PARSER_REQUIRE_PARSING
	* @access protected 
	*/
	function pre_parse()
	{
		$field = $this->attributes['for'];
		if (empty($field))
		{
			error('MISSINGREQUIREATTRIBUTE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'attribute' => 'for',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 

		$this->field = $field;

		return PARSER_REQUIRE_PARSING;
	} 

	/**
	* 
	* @param code $ _writer
	* @return void 
	* @access protected 
	*/
	function pre_generate(&$code)
	{
		parent::pre_generate($code);

		$tempvar = $code->get_temp_variable();
		$code->write_php('$' . $tempvar . ' = trim(' . $this->get_dataspace_ref_code() . '->get(\'' . $this->field . '\'));');
		$code->write_php('if (!empty($' . $tempvar . ')) {');
	} 

	/**
	* 
	* @param code $ _writer
	* @return void 
	* @access protected 
	*/
	function post_generate(&$code)
	{
		$code->write_php('}');
		parent::post_generate($code);
	} 
} 

?>