<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: image_select.tag.php 21 2004-03-05 11:43:13Z server $
*
***********************************************************************************/

require_once(LIMB_DIR . '/core/template/tags/form/control_tag.class.php');

class js_checkbox_tag_info
{
	var $tag = 'js_checkbox';
	var $end_tag = ENDTAG_FORBIDDEN;
	var $tag_class = 'js_checkbox_tag';
} 

register_tag(new js_checkbox_tag_info());

class js_checkbox_tag extends control_tag
{
	var $runtime_component_path = '/core/template/components/form/js_checkbox_component';
	
	function get_rendered_tag()
	{
		return 'input';
	}
	
	function pre_generate(&$code)
	{
		$this->attributes['type'] = 'hidden';

		parent :: pre_generate($code);
	}
	
	function generate_contents(&$code)
	{
		parent :: generate_contents($code);
		
		$code->write_php($this->get_component_ref_code() . '->render_js_checkbox();');
	}
} 

?>