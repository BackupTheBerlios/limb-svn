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

class FormProcessingCommand// implements Command
{
  var $form_id;
  var $is_multi;
  var $validator;

  function FormProcessingCommand($form_id, $is_multi = LIMB_SINGLE_FORM)
  {
    $this->form_id = $form_id;
    $this->is_multi = $is_multi;
  }

  function perform()
  {
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();

    $dataspace =& $this->_switchDataSpace();
    $form_component =& $this->getFormComponent();

    if($this->_isFirstTime($request))
    {
      $result = $this->_initializeDataspace($dataspace);
      $form_component->registerDataSource($dataspace);
      return $result;
    }
    else
    {
      $this->_mergeDataspaceWithRequest($dataspace, $request);
      $form_component->registerDataSource($dataspace);
      return $this->_validate(&$dataspace);
    }
  }

  function _initializeDataspace(&$dataspace)
  {
    return LIMB_STATUS_FORM_DISPLAYED;
  }

  function _validate(&$dataspace)
  {
    $validator =& $this->getValidator($dataspace);

    $validator->validate($dataspace);

    if(!$validator->IsValid())
    {
      $form_component =& $this->getFormComponent();
      $form_component->setErrors($validator->getErrorList());
      return LIMB_STATUS_FORM_NOT_VALID;
    }
    else
      return LIMB_STATUS_FORM_SUBMITTED;
  }

  function & getFormComponent()
  {
    $toolkit =& Limb :: toolkit();
    $view =& $toolkit->getView();
    return $view->findChild($this->form_id);
  }

  function & getValidator(&$dataspace)
  {
    if(is_object($this->validator))
      return $this->validator;

    include_once(WACT_ROOT . '/validation/validator.inc.php');
    $this->validator = new Validator();

    $this->_registerValidationRules($this->validator, $dataspace);

    return $this->validator;
  }

  function setValidator(&$validator)
  {
    $this->validator =& $validator;
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

  function _htmlspecialcharsDataspaceValue(&$dataspace, $name)
  {
    if(!$value = $dataspace->get($name))
      return;

    $this->dataspace->set($name, htmlspecialchars($value));
  }
}


?>
