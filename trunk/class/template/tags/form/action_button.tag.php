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

require_once(LIMB_DIR . '/class/template/tags/form/button.tag.php');

class action_button_tag_info
{
	var $tag = 'action_button';
	var $end_tag = ENDTAG_FORBIDDEN;
	var $tag_class = 'action_button_tag';
} 

register_tag(new action_button_tag_info());

class action_button_tag extends button_tag
{
	var $runtime_component_path = '/class/template/components/form/input_submit_component';
	
	function check_nesting_level()
	{
		if (!isset($this->attributes['action']))
		{
			error('ATTRIBUTE_REQUIRED', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'attribute' => 'action',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	}
	
	function prepare()
	{
		parent :: prepare();
		
		if(!isset($this->attributes['type']))
			$this->attributes['type'] = 'submit';	

		$this->attributes['onclick'] = "add_form_hidden_parameter(this.form, 'action', '{$this->attributes['action']}');";
		
		if(isset($this->attributes['reload_parent']))
		{
			$this->attributes['onclick'] .= "add_form_action_parameter(this.form, 'reload_parent', '1')";
		unset($this->attributes['reload_parent']);
		}
		
	unset($this->attributes['action']);
	}
	
	function get_rendered_tag()
	{
		return 'input';
	}	
} 

?>