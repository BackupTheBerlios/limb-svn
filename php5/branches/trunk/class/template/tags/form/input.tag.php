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

class input_tag_info
{
	public $tag = 'input';
	public $end_tag = ENDTAG_FORBIDDEN;
	public $tag_class = 'input_tag';
} 

register_tag(new input_tag_info());

/**
* Compile time component for building runtime Inputcomponents
* Creates all the components beginning with the name Input
*/
class input_tag extends control_tag
{
	/**
	* Sets the runtime_component_path property, depending on the type of
	* Input tag
	*/
	public function prepare()
	{
		$type = strtolower($this->attributes['type']);

		switch ($type)
		{
			case 'text':
				$this->runtime_component_path = dirname(__FILE__) . '/../../components/form/input_text_component';
				break;
			case 'password':
				$this->runtime_component_path = dirname(__FILE__) . '/../../components/form/input_password_component';
				break;
			case 'checkbox':
				$this->runtime_component_path = dirname(__FILE__) . '/../../components/form/input_checkbox_component';
				break;
			case 'submit':
				$this->runtime_component_path = dirname(__FILE__) . '/../../components/form/input_submit_component';
				break;
			case 'radio':
				$this->runtime_component_path = dirname(__FILE__) . '/../../components/form/input_radio_component';
				break;
			case 'reset':
				$this->runtime_component_path = dirname(__FILE__) . '/../../components/form/input_reset_component';
				break;
			case 'file':
				$this->runtime_component_path = dirname(__FILE__) . '/../../components/form/input_file_component';
				break;
			case 'hidden':
				$this->runtime_component_path = dirname(__FILE__) . '/../../components/form/input_hidden_component';
				break;
			case 'image':
				$this->runtime_component_path = dirname(__FILE__) . '/../../components/form/input_image_component';
				break;
			case 'button':
				$this->runtime_component_path = dirname(__FILE__) . '/../../components/form/input_button_component';
				break;
			default:
				error('UNKNOWNINPUTYPE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
				array(
						'file' => $this->source_file,
						'line' => $this->starting_line_no,
						'type' => $type));
		} 

		parent :: prepare();
	} 
} 

?>