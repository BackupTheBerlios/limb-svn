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


class core_block_tag_info
{
	var $tag = 'core:BLOCK';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'core_block_tag';
} 

register_tag(new core_block_tag_info());

class core_block_tag extends server_component_tag
{
  
  function core_block_tag()
  {
	  $this->runtime_component_path = dirname(__FILE__) . '/../../components/block_component';
	}
	/**
	* 
	* @param code_writer $ 
	* @return void 
	* @access protected 
	*/
	function generate_constructor(&$code)
	{
		parent::generate_constructor($code);
		if (array_key_exists('hide', $this->attributes))
		{
			$code->write_php($this->get_component_ref_code() . '->visible = false;');
		} 
	} 
	/**
	* 
	* @param code_writer $ 
	* @return void 
	* @access protected 
	*/
	function pre_generate(&$code)
	{
		parent::pre_generate($code);
		$code->write_php('if (' . $this->get_component_ref_code() . '->is_visible()) {');
	} 
	/**
	* 
	* @param code_writer $ 
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