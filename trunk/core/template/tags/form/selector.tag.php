<?php
require_once(LIMB_DIR . '/core/template/tags/form/control_tag.class.php');

class selector_tag_info
{
	var $tag = 'selector';
	var $end_tag = ENDTAG_FORBIDDEN;
	var $tag_class = 'selector_tag';
} 

register_tag(new selector_tag_info());

class selector_tag extends control_tag
{
	var $runtime_component_path = '/core/template/components/form/input_checkbox_component';
	
	function prepare()
	{
		$this->attributes['type'] = 'checkbox';
		
		if(!isset($this->attributes['selector_name']))
			$this->attributes['name'] = 'selector_name';
		else
			$this->attributes['name'] = $this->attributes['selector_name'];
			
		unset($this->attributes['selector_name']);
	}
	
	function get_rendered_tag()
	{
		return 'input';
	}
	
	function pre_generate(&$code)
	{
		$name = '$' . $code->get_temp_variable();
		$parent = $this->get_dataspace_ref_code();
		$ref = $this->get_component_ref_code();
		
		$code->write_php("
		
		if ({$name} = {$parent}->get('" . $this->attributes['name']. "'))
			{$ref}->set_attribute('name', {$name});
		");	
		
		parent :: pre_generate($code);
	}
} 

?>