<?php

require_once(LIMB_DIR . '/core/template/tags/form/control_tag.class.php');

class richedit_tag_info
{
	var $tag = 'richedit';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'richedit_tag';
} 

register_tag(new richedit_tag_info());

class richedit_tag extends control_tag
{
	var $runtime_component_path = '/core/template/components/form/richedit_component';
	
	function get_rendered_tag()
	{
		return 'textarea';
	}
	
	function pre_generate(&$code)
	{
		$code->write_php($this->get_component_ref_code() . '->init_richedit();');
		
		parent :: pre_generate($code);
	} 
		
	function generate_contents(&$code)
	{
		$code->write_php($this->get_component_ref_code() . '->render_contents();');
	}
} 

?>