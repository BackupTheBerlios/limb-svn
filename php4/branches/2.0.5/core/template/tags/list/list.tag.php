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


class list_list_tag_info
{
	var $tag = 'list:LIST';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'list_list_tag';
} 

register_tag(new list_list_tag_info());

/**
* The parent compile time component for lists
*/
class list_list_tag extends server_component_tag
{
	var $runtime_component_path = '/core/template/components/list_component';

	/**
	* 
	* @param code $ _writer
	* @return void 
	* @access protected 
	*/
	function pre_generate(&$code)
	{
		parent::pre_generate($code);
		$code->write_php($this->get_component_ref_code() . '->prepare();');

		$code->write_php('if (' . $this->get_component_ref_code() . '->next()) {');
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

		$emptychild = &$this->find_immediate_child_by_class('list_default_tag');
		if ($emptychild)
		{
			$code->write_php(' else {');

			$emptychild->generate_now($code);
			$code->write_php('}');
		} 
		parent::post_generate($code);
	} 

	/**
	* 
	* @return list _list_tag this instance
	* @access protected 
	*/
	function &get_dataspace()
	{
		return $this;
	} 

	/**
	* 
	* @return string PHP runtime variable reference to component
	* @access protected 
	*/
	function get_dataspace_ref_code()
	{
		return $this->get_component_ref_code() . '->dataset';
	} 
} 

?>