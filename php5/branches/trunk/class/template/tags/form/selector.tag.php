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

class selector_tag_info
{
	public $tag = 'selector';
	public $end_tag = ENDTAG_FORBIDDEN;
	public $tag_class = 'selector_tag';
} 

register_tag(new selector_tag_info());

class selector_tag extends control_tag
{
  public function __construct()
  {
	  $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/input_checkbox_component';
	}
	
	public function prepare()
	{
		$this->attributes['type'] = 'checkbox';
		
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
		$name = '$' . $code->get_temp_variable();
		$parent = $this->get_dataspace_ref_code();
		$ref = $this->get_component_ref_code();
		
		$code->write_php("
		
		if ({$name} = {$parent}->get('" . $this->attributes['name']. "'))
			{$ref}->set('name', {$name});
		");	
		
		parent :: pre_generate($code);
	}
} 

?>