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
class metadata_breadcrumbs_tag_info
{
	public $tag = 'metadata:BREADCRUMBS';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'metadata_breadcrumbs_tag';
} 

register_tag(new metadata_breadcrumbs_tag_info());

class metadata_breadcrumbs_tag extends server_component_tag
{
  public function __construct()
  {
	  $this->runtime_component_path = dirname(__FILE__) . '/../../components/metadata_component';
	}
		
	public function generate_contents($code)
	{
		$child_list = $this->find_immediate_child_by_class('grid_list_tag');

		if(isset($this->attributes['request_path_attribute']))	
			$code->write_php($this->get_component_ref_code() . '->set_request_path("' . $this->attributes['request_path_attribute']. '");');
		
		if(isset($this->attributes['offset_path']))	
			$code->write_php($this->get_component_ref_code() . '->set_offset_path("' . $this->attributes['offset_path'] . '");');
			
		if ($child_list)
			$code->write_php($child_list->get_component_ref_code() . '->register_dataset(' . $this->get_component_ref_code() . '->get_breadcrumbs_dataset());');
		
		parent :: generate_contents($code);
	}
} 

?>