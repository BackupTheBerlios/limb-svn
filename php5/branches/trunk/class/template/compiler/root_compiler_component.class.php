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
require_once(LIMB_DIR . 'class/template/compiler/compiler_directive_tag.class.php');

/**
* The root compile time component in the template hierarchy. Used to generate
* the correct reference PHP code like $dataspace->...
*/
class root_compiler_component extends compiler_directive_tag
{
	/**
	* Calls the parent pre_generate() method then writes
	* "$dataspace->prepare();" to the compiled template.
	*/
	public function pre_generate($code)
	{
		parent::pre_generate($code);
		
		if($this->is_debug_enabled())
		{
			$code->write_html("<div class='debug-tmpl-main'>");
			
			$this->_generate_debug_editor_link_html($code, $this->source_file);
		}
	} 
	
	public function post_generate($code)
	{
		if($this->is_debug_enabled())
		{
			$code->write_html('</div>');
		}
		
		parent :: post_generate($code);
	}

	/**
	* Returns the base for building the PHP runtime component reference string
	*/
	public function get_component_ref_code()
	{
		return '$dataspace';
	} 

	/**
	* Returns $dataspace
	*/
	public function get_dataspace_ref_code()
	{
		return '$dataspace';
	} 

	/**
	* Returns this instance of root_compiler_component
	*/
	public function get_dataspace()
	{
		return $this;
	} 
} 

?>
