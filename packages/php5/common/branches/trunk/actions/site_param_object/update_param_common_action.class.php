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
require_once(LIMB_DIR . 'core/lib/util/complex_array.class.php');
require_once(LIMB_DIR . 'class/core/actions/form_action.class.php');
require_once(LIMB_DIR . 'core/model/sys_param.class.php');

class update_param_common_action extends form_action
{
	protected $params_type = array();

	function __construct()
	{
	  parent :: __construct();
    
    $this->params_type = $this->_define_params_type();
	}
	
	protected function _define_dataspace_name()
	{
	  return 'site_param_form';
	}
	
	protected function _define_params_type()
	{
	  return array(
			'site_title' => 'char',
			'contact_email' => 'char',
		);
	}
	
	protected function _init_validator()
	{
    $this->validator->add_rule($v = array(LIMB_DIR . 'class/validators/rules/email_rule', 'contact_email')); 
	}

	protected function _init_dataspace($request)
	{
		$sys_param = sys_param :: instance();

		$data = array();
		foreach($this->params_type as $param_name => $param_type)
			$data[$param_name] = $sys_param->get_param($param_name, $param_type);
		
		$this->dataspace->import($data);		
	}

	protected function _valid_perform($request, $response)
	{
		$data = $this->dataspace->export();
		$sys_param = sys_param :: instance();

		foreach($this->params_type as $param_name => $param_type)
			$sys_param->save_param($param_name, $param_type, $data[$param_name]);

		$request->set_status(request :: STATUS_FORM_SUBMITTED);
	}
}
?>