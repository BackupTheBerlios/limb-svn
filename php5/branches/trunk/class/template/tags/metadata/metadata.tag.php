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

class metadata_metadata_tag_info
{
	var $tag = 'METADATA:METADATA';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'metadata_metadata_tag';
} 

register_tag(new metadata_metadata_tag_info());

class metadata_metadata_tag extends server_component_tag
{
  function metadata_metadata_tag()
  {
	  $this->runtime_component_path = dirname(__FILE__) . '/../../components/metadata_component';
	}
		
	function generate_contents(&$code)
	{				
		$ref = $this->get_component_ref_code();
		$code->write_php("{$ref}->load_metadata();\n");
			
		parent :: generate_contents($code);
		
	} 

	function &get_dataspace()
	{
		return $this;
	} 

	function get_dataspace_ref_code()
	{
		return $this->get_component_ref_code();
	} 

} 

?>