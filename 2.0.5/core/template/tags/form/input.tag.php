<?php
require_once(LIMB_DIR . '/core/template/tags/form/control_tag.class.php');

class input_tag_info
{
	var $tag = 'input';
	var $end_tag = ENDTAG_FORBIDDEN;
	var $tag_class = 'input_tag';
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
	* 
	* @return void 
	* @access protected 
	*/
	function prepare()
	{
		$type = strtolower($this->attributes['type']);

		switch ($type)
		{
			case 'text':
				$this->runtime_component_path = '/core/template/components/form/input_text_component';
				break;
			case 'password':
				$this->runtime_component_path = '/core/template/components/form/input_password_component';
				break;
			case 'checkbox':
				$this->runtime_component_path = '/core/template/components/form/input_password_component';
				break;
			case 'submit':
				$this->runtime_component_path = '/core/template/components/form/input_submit_component';
				break;
			case 'radio':
				$this->runtime_component_path = '/core/template/components/form/input_radio_component';
				break;
			case 'reset':
				$this->runtime_component_path = '/core/template/components/form/input_reset_component';
				break;
			case 'file':
				$this->runtime_component_path = '/core/template/components/form/input_file_component';
				break;
			case 'hidden':
				$this->runtime_component_path = '/core/template/components/form/input_hidden_component';
				break;
			case 'image':
				$this->runtime_component_path = '/core/template/components/form/input_image_component';
				break;
			case 'button':
				$this->runtime_component_path = '/core/template/components/form/input_button_component';
				break;
			default:
				error('UNKNOWNINPUTYPE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
				array(
						'file' => $this->source_file,
						'line' => $this->starting_line_no,
						'type' => $type));
		} 

		parent::prepare();
	} 
} 

?>