<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . '/class/core/commands/command.interface.php');

class form_command implements Command
{
  protected $form_name;

	function __construct($form_name)
	{
    $this->form_name = $form_name;
	}
  
  //for mocking
  protected function _get_validator()
  {
    include_once(LIMB_DIR . '/class/validators/validator.class.php');
    return new validator();
  }
  				
	protected function _is_first_time($request)
	{
    $arr = $request->get($this->form_name);
    if(isset($arr['submitted']) && $arr['submitted'])
      return false;
    else
      return true;    
	} 

	protected function _register_validation_rules($validator, $dataspace)
	{
	} 
  
	protected function _define_datamap()
	{
    return array();
	}   
  
	public function validate($dataspace)
	{
    $validator = $this->_get_validator();
    
    $this->_register_validation_rules($validator, $dataspace);
    
    return $validator->validate($dataspace);
	} 

	public function perform()
	{
    $request = Limb :: toolkit()->getRequest();
    
    $dataspace = Limb :: toolkit()->switchDataspace($this->form_name);
    
		if ($this->_is_first_time($request))
		{
      $this->_init_first_time_dataspace($dataspace, $request);
      
			return Limb :: STATUS_FORM_DISPLAYED;
		} 
		else
		{
      $this->_merge_dataspace_with_request($dataspace, $request);
      
			if(!$this->validate($dataspace))
			  return Limb :: STATUS_FORM_NOT_VALID;
			else	
        return Limb :: STATUS_FORM_SUBMITTED;
		} 
	}

  protected function _init_first_time_dataspace($dataspace, $request)
  {
  }
  
  protected function _merge_dataspace_with_request($dataspace, $request)
  {
		complex_array :: map($this->_define_datamap(), $request->get($this->form_name), $data = array());
    
    $dataspace->merge($data);
  }
} 


?>