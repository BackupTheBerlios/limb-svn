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
require_once(LIMB_DIR . '/class/template/tags/form/control_tag.class.php');

class image_select_tag_info
{
	public $tag = 'image_select';
	public $end_tag = ENDTAG_FORBIDDEN;
	public $tag_class = 'image_select_tag';
} 

register_tag(new image_select_tag_info());

class image_select_tag extends control_tag
{
  public function __construct()
  {
	  $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/image_select_component';
	}
	
	public function get_rendered_tag()
	{
		return 'input';
	}
	
	public function pre_generate($code)
	{
		$this->attributes['type'] = 'hidden';
			
		$code->write_php($this->get_component_ref_code() . '->init_image_select();');
		
		parent :: pre_generate($code);
	}
	
	public function generate_contents($code)
	{
		parent :: generate_contents($code);
		
		$code->write_php($this->get_component_ref_code() . '->render_image_select();');
	}
} 

?>