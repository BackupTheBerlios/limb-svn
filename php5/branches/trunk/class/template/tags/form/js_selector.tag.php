<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: selector.tag.php 21 2004-03-05 11:43:13Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/template/tags/form/control_tag.class.php');

class js_selector_tag_info
{
	public $tag = 'js_selector';
	public $end_tag = ENDTAG_FORBIDDEN;
	public $tag_class = 'js_selector_tag';
} 

register_tag(new js_selector_tag_info());

class js_selector_tag extends control_tag
{
  public function __construct()
  {
	  $this->runtime_component_path = dirname(__FILE__) . '/../../components/js_checkbox_component';
	}

	public function prepare()
	{
		if(!isset($this->attributes['selector_name']))
			$this->attributes['name'] = 'selector_name';
		else
			$this->attributes['name'] = $this->attributes['selector_name'];
			
	  unset($this->attributes['selector_name']);
	}
		
	public function get_rendered_tag()
	{
		return 'input';
	}
	
	public function pre_generate($code)
	{
		$this->attributes['type'] = 'hidden';

		$name = '$' . $code->get_temp_variable();
		$parent = $this->get_dataspace_ref_code();
		$ref = $this->get_component_ref_code();
		
		$code->write_php("
		
		if ({$name} = {$parent}->get('" . $this->attributes['name']. "'))
			{$ref}->set_attribute('name', {$name});
		");	
		
		parent :: pre_generate($code);
	}
	
	public function generate_contents($code)
	{
		parent :: generate_contents($code);
		
		$code->write_php($this->get_component_ref_code() . '->render_js_checkbox();');
	}
} 

?>