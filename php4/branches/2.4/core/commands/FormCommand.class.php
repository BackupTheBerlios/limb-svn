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
define('LIMB_ANY_FORM_WILDCARD', '*');
define('LIMB_MULTI_FORM', true);
define('LIMB_SINGLE_FORM', false);

require_once(LIMB_DIR . '/core/util/ComplexArray.class.php');

class FormCommand// implements Command
{
  var $form_id;
  var $is_multi;
  var $validator;

  function FormCommand($form_id, $is_multi = LIMB_SINGLE_FORM)
  {
    $this->form_id = $form_id;
    $this->is_multi = $is_multi;
  }

  function & getFormComponent()
  {
    $toolkit =& Limb :: toolkit();
    $view =& $toolkit->getView();
    return $view->findChild($this->form_id);
  }

  //for mocking
  function & _getValidator()
  {
    if($this->validator)
      return $this->validator;

    include_once(WACT_ROOT . '/validation/validator.inc.php');
    $this->validator = new Validator();
    return $this->validator;
  }

  function _isFirstTime(&$request)
  {
    $arr = $this->_getRequestData($request);
    if(isset($arr['submitted']) &&  $arr['submitted'])
      return false;
    else
      return true;
  }

  function _registerValidationRules(&$validator, &$dataspace)
  {
  }

  function _defineDatamap()
  {
    return array();
  }

  function validate(&$dataspace)
  {
    $validator =& $this->_getValidator();

    $this->_registerValidationRules($validator, $dataspace);

    $validator->validate($dataspace);

    return $validator->IsValid();
  }

  function perform()
  {
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();

    $dataspace =& $this->_switchDataSpace();
    $form_component =& $this->getFormComponent();

    if($this->_isFirstTime($request))
    {
      $this->_initFirstTimeDataspace($dataspace, $request);

      $form_component->registerDataSource($dataspace);

      return LIMB_STATUS_FORM_DISPLAYED;
    }
    else
    {
      $this->_mergeDataspaceWithRequest($dataspace, $request);

      $form_component->registerDataSource($dataspace);

      if(!$this->validate($dataspace))
      {
        $validator =& $this->_getValidator();
        $form_component->setErrors($validator->getErrorList());
        return LIMB_STATUS_FORM_NOT_VALID;
      }
      else
        return LIMB_STATUS_FORM_SUBMITTED;
    }
  }

  function _initFirstTimeDataspace(&$dataspace, &$request)
  {
  }

  function _mergeDataspaceWithRequest(&$dataspace, &$request)
  {
    ComplexArray :: map($this->_defineDatamap(), $this->_getRequestData($request), $data = array());

    $dataspace->merge($data);
  }

  function _getRequestData(&$request)
  {
    if($this->is_multi)
      return $request->get($this->form_id);
    else
      return $request->export();
  }

  function &_switchDataSpace()
  {
    $toolkit =& Limb :: toolkit();

    if($this->is_multi)
      return $toolkit->switchDataspace($this->form_id);
    else
      return $toolkit->getDataspace();
  }
}


?>
