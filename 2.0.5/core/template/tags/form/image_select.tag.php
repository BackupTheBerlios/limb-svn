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


require_once(LIMB_DIR . '/core/template/tags/form/control_tag.class.php');

class image_select_tag_info
{
	var $tag = 'image_select';
	var $end_tag = ENDTAG_FORBIDDEN;
	var $tag_class = 'image_select_tag';
} 

register_tag(new image_select_tag_info());

class image_select_tag extends control_tag
{
	var $runtime_component_path = '/core/template/components/form/image_select_component';
	
	function get_rendered_tag()
	{
		return 'input';
	}
	
	function pre_generate(&$code)
	{
		$this->attributes['type'] = 'hidden';
			
		$code->write_php($this->get_component_ref_code() . '->init_image_select();');
		
		parent :: pre_generate($code);
	}
	
	function generate_contents(&$code)
	{
		parent :: generate_contents($code);
		
		$code->write_php($this->get_component_ref_code() . '->render_image_select();');
	}
} 

?>