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


require_once(LIMB_DIR . 'core/template/compiler/compiler_directive_tag.class.php');

/**
* The root compile time component in the template hierarchy. Used to generate
* the correct reference PHP code like $dataspace->...
*/
class root_compiler_component extends compiler_directive_tag
{
	/**
	* Calls the parent pre_generate() method then writes
	* "$dataspace->prepare();" to the compiled template.
	* 
	* @param code $ _writer
	* @return void 
	* @access protected 
	*/
	function pre_generate(&$code)
	{
		parent::pre_generate($code);
		$code->write_php($this->get_dataspace_ref_code() . '->prepare();');
	} 

	/**
	* Returns the base for building the PHP runtime component reference string
	* 
	* @param code $ _writer
	* @return string $dataspace
	* @access protected 
	*/
	function get_component_ref_code()
	{
		return '$dataspace';
	} 

	/**
	* Returns $dataspace
	* 
	* @param code $ _writer
	* @return string $dataspace
	* @access protected 
	*/
	function get_dataspace_ref_code()
	{
		return '$dataspace';
	} 

	/**
	* Returns this instance of root_compiler_component
	* 
	* @return component _tree this instance
	* @access protected 
	*/
	function &get_dataspace()
	{
		return $this;
	} 
} 

?>