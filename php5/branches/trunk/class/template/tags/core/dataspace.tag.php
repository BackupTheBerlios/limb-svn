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
class core_dataspace_tag_info
{
	public $tag = 'core:DATASPACE';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'core_dataspace_tag';
} 

register_tag(new core_dataspace_tag_info());

/**
* Dataspaces act is "namespaces" for a template.
*/
class core_dataspace_tag extends server_component_tag
{
  public function __construct()
  {
	  $this->runtime_component_path = dirname(__FILE__) . '/../../components/dataspace_component';
	}

	public function pre_generate($code)
	{
		parent :: pre_generate($code);

		$code->write_php('if (!' . $this->get_dataspace_ref_code() . '->is_empty()){');
	} 

	public function post_generate($code)
	{
		$code->write_php('}');
		
		parent :: post_generate($code);
	} 

	public function get_dataspace()
	{
		return $this;
	} 

	public function get_dataspace_ref_code()
	{
		return $this->get_component_ref_code();
	} 
} 

?>