<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/commands/Command.interface.php');

class FormCommand implements Command
{
  var $form_name;

  function FormCommand($form_name)
  {
    $this->form_name = $form_name;
  }

  //for mocking
  function _getValidator()
  {
    include_once(LIMB_DIR . '/class/validators/Validator.class.php');
    return new Validator();
  }

  function _isFirstTime($request)
  {
    $arr = $request->get($this->form_name);
    if(isset($arr['submitted']) &&  $arr['submitted'])
      return false;
    else
      return true;
  }

  function _registerValidationRules($validator, $dataspace)
  {
  }

  function _defineDatamap()
  {
    return array();
  }

  function validate($dataspace)
  {
    $validator = $this->_getValidator();

    $this->_registerValidationRules($validator, $dataspace);

    return $validator->validate($dataspace);
  }

  function perform()
  {
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();

    $dataspace =& $toolkit->switchDataspace($this->form_name);

    if ($this->_isFirstTime($request))
    {
      $this->_initFirstTimeDataspace($dataspace, $request);

      return Limb :: STATUS_FORM_DISPLAYED;
    }
    else
    {
      $this->_mergeDataspaceWithRequest($dataspace, $request);

      if(!$this->validate($dataspace))
        return Limb :: STATUS_FORM_NOT_VALID;
      else
        return Limb :: STATUS_FORM_SUBMITTED;
    }
  }

  function _initFirstTimeDataspace($dataspace, $request)
  {
  }

  function _mergeDataspaceWithRequest($dataspace, $request)
  {
    ComplexArray :: map($this->_defineDatamap(), $request->get($this->form_name), $data = array());

    $dataspace->merge($data);
  }
}


?>