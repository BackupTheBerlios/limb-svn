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
require_once(LIMB_DIR . '/class/template/tags/form/control_tag.class.php');

class color_picker_tag_info
{
	public $tag = 'color_picker';
	public $end_tag = ENDTAG_FORBIDDEN;
	public $tag_class = 'color_picker_tag';
} 

register_tag(new color_picker_tag_info());

class color_picker_tag extends control_tag
{
  public function __construct()
  {
	  $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/color_picker_component';
	}
	
	public function get_rendered_tag()
	{
		return 'input';
	}
	
	public function pre_generate($code)
	{
		$code->write_php($this->get_component_ref_code() . '->init_color_picker();');
		
		parent :: pre_generate($code);
	}
	
	public function generate_contents($code)
	{
		parent :: generate_contents($code);
		
		$code->write_php($this->get_component_ref_code() . '->render_color_picker();');
	}
} 

?>