<?php

require_once(LIMB_DIR . '/core/template/tags/form/control_tag.class.php');

class text_area_tag_info
{
	var $tag = 'textarea';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'text_area_tag';
} 

register_tag(new text_area_tag_info());

class text_area_tag extends control_tag
{
	var $runtime_component_path = '/core/template/components/form/text_area_component';
		
	function generate_contents(&$code)
	{
		$code->write_php($this->get_component_ref_code() . '->render_contents();');
	} 
} 

?>