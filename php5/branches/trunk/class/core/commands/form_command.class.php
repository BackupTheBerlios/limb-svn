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
require_once(LIMB_DIR . 'class/core/commands/command.interface.php');

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
    include_once(LIMB_DIR . 'class/validators/validator.class.php');
    return new validator();
  }
  				
	protected function _is_first_time()
	{
    if(Limb :: toolkit()->getDataspace()->get('submitted'))
      return false;
    else
      return true;    
	} 

	protected function _register_validation_rules($validator)
	{
	} 
  
  protected function _merge_dataspace_with_request()
  {
    $request = Limb :: toolkit()->getRequest();
    $dataspace = Limb :: toolkit()->getDataspace();
    
    if ($arr = $request->get($this->form_name))
			$dataspace->merge($arr);    
  }

	public function validate()
	{
    $validator = $this->_get_validator();
    
    $this->_register_validation_rules($validator);
    
    return $validator->validate(Limb :: toolkit()->getDataspace());
	} 

	public function perform()
	{ 
    $this->_merge_dataspace_with_request();
    
		if ($this->_is_first_time())
		{
			return Limb :: STATUS_FORM_DISPLAYED;
		} 
		else
		{
			if(!$this->validate())
			  return Limb :: STATUS_FORM_NOT_VALID;
			else	
        return Limb :: STATUS_FORM_SUBMITTED;
		} 
	}
} 


?>