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
class metadata_title_tag_info
{
	public $tag = 'METADATA:TITLE';
	public $end_tag = ENDTAG_FORBIDDEN;
	public $tag_class = 'metadata_title_tag';
} 

register_tag(new metadata_title_tag_info());

class metadata_title_tag extends server_component_tag
{
  public function __construct()
  {
	  $this->runtime_component_path = dirname(__FILE__) . '/../components/metadata_component';
	}
		
	public function generate_contents($code)
	{				
		$ref = $this->get_component_ref_code();
		
		if(isset($this->attributes['separator']))
		{
			$code->write_php("{$ref}->set_title_separator(\"". $this->attributes['separator'] ."\");\n");
		}	

		if(isset($this->attributes['offset_path']))	
			$code->write_php($this->get_component_ref_code() . '->set_offset_path("' . $this->attributes['offset_path'] . '");');
		
		$ref = $this->get_component_ref_code();
		$code->write_php("echo {$ref}->get_title();\n");
			
		parent :: generate_contents($code);
	} 
} 

?>