<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: set_metadata_action.class.php 38 2004-03-13 14:25:46Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/lib/util/complex_array.class.php');
require_once(LIMB_DIR . 'core/actions/form_action.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/email_rule.class.php');
require_once(LIMB_DIR . 'core/model/sys_param.class.php');

class update_action extends form_action
{
	var $definition = array(
		'params_type' => array(
			'site_title' => 'char',
			'contact_email' => 'char',
		),
	);
	
	function update_action($name='site_param_form',$merge_definition=array())
	{
		$this->definition = complex_array :: array_merge($this->definition, $merge_definition);
		
		parent :: form_action($name);
	}
	
	function _init_validator()
	{
		$this->validator->add_rule(new email_rule('contact_email'));
	}

	function _init_dataspace()
	{
		$sys_param =& sys_param :: instance();

		$data = array();
		foreach($this->definition['params_type'] as $param_name => $param_type)
			$data[$param_name] = $sys_param->get_param($param_name, $param_type);
		
		$this->_import($data);		
	}

	function _valid_perform()
	{
		$data = $this->dataspace->export();
		$sys_param =& sys_param :: instance();

		foreach($this->definition['params_type'] as $param_name => $param_type)
			$sys_param->save_param($param_name, $param_type, $data[$param_name]);

		return new response(RESPONSE_STATUS_FORM_SUBMITTED);
	}
}
?>