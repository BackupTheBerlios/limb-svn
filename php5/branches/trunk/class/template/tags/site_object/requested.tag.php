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
class site_object_requested_tag_info
{
	public $tag = 'site_object:REQUESTED';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'site_object_requested_tag';
} 

register_tag(new site_object_requested_tag_info());

class site_object_requested_tag extends server_component_tag
{	
  public function __construct()
  {
	  $this->runtime_component_path = dirname(__FILE__) . '/../../components/site_object_component';
	}
  
	public function generate_contents($code)
	{
		$code->write_php($this->get_component_ref_code() . '->fetch_requested();');
		
		parent :: generate_contents($code);
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