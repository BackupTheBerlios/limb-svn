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

class file_select_tag_info
{
	var $tag = 'file_select';
	var $end_tag = ENDTAG_FORBIDDEN;
	var $tag_class = 'file_select_tag';
} 

register_tag(new file_select_tag_info());

class file_select_tag extends control_tag
{
	var $runtime_component_path = '/class/template/components/form/file_select_component';
	
	function get_rendered_tag()
	{
		return 'input';
	}
	
	function pre_generate(&$code)
	{
		$this->attributes['type'] = 'hidden';
			
		$code->write_php($this->get_component_ref_code() . '->init_file_select();');
		
		parent :: pre_generate($code);
	}
	
	function generate_contents(&$code)
	{
		parent :: generate_contents($code);
		
		$code->write_php($this->get_component_ref_code() . '->render_file_select();');
	}
} 

?>