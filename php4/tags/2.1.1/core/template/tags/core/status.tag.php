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
	var $tag = 'core:STATUS';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'core_status_tag';
} 

register_tag(new core_status_tag_info());

/**
* Defines an action take, should a dataspace variable have been set at runtime.
* The opposite of the core_default_tag
*/
class core_status_tag extends compiler_directive_tag
{
	var $const;

	/**
	* 
	* @return int PARSER_REQUIRE_PARSING
	* @access protected 
	*/
	function pre_parse()
	{
		if (!isset($this->attributes['name']))
		{
			error('MISSINGREQUIREATTRIBUTE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'attribute' => 'name',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
		
		if(!defined($this->attributes['name']))
		{
			error('CONSTNOTDEFINED', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'const' => $this->attributes['name'],
					'file' => $this->source_file,
					'line' => $this->starting_line_no));		
		}

		$this->const = $this->attributes['name'];

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
		
		$value = 'true';
		if (isset($this->attributes['value']) && !(boolean)$this->attributes['value'])
			$value = 'false';

		$tempvar = $code->get_temp_variable();
		$code->write_php('$' . $tempvar . ' = trim(' . $this->get_dataspace_ref_code() . '->get("status"));');
		$code->write_php('if ((boolean)(constant("' . $this->const . '") & $' . $tempvar . ') === ' . $value . ') {');
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