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

/**
* Server tag component tags are server_component_tags which also correspond to
* an HTML tag. Makes it easier to implement instead of extending from the
* server_component_tag class
*/
abstract class server_tag_component_tag extends server_component_tag
{
	/**
	* Returns the XML tag name
	*/
	public function get_rendered_tag()
	{
		return $this->tag;
	} 

	/**
	* Adds any additional XML attributes
	*/
	public function generate_extra_attributes($code)
	{
	} 

	/**
	* Calls the parent pre_generate() method then writes the XML tag name
	* plus a PHP string which renders the attributes from the runtime
	* component.
	*/
	public function pre_generate($code)
	{
		parent::pre_generate($code);
		$code->write_html('<' . $this->get_rendered_tag());
		$code->write_php($this->get_component_ref_code() . '->render_attributes();'); 
		$this->generate_extra_attributes($code);
		$code->write_html('>');
	} 

	/**
	* Writes the closing tag string to the compiled template
	*/
	public function post_generate($code)
	{
		if ($this->has_closing_tag)
		{
			$code->write_html('</' . $this->get_rendered_tag() . '>');
		} 
		parent::post_generate($code);
	} 

	/**
	* Writes the compiled template constructor from the runtime component,
	* assigning the attributes found at compile time to the runtime component
	* via a serialized string
	*/
	public function generate_constructor($code)
	{
		parent::generate_constructor($code);
		$code->write_php($this->get_component_ref_code() . '->attributes = ' . var_export($this->attributes, true) . ';');
	} 
} 

?>