<?php

class FormValidateCommand
{
  var $form_id;
  var $validator;

  function FormValidateCommand($form_id)
  {
    $this->form_id = $form_id;
  }

  function perform()
  {
    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->getDataspace();

    $validator =& $this->getValidator($dataspace);

    $this->_registerValidationRules($validator, $dataspace);

    $validator->validate($dataspace);

    if(!$validator->IsValid())
    {
      $form_component =& $this->getFormComponent();
      $form_component->registerDataSource($dataspace);
      $form_component->setErrors($validator->getErrorList());

      return LIMB_STATUS_FORM_NOT_VALID;
    }
    else
      return LIMB_STATUS_OK;
  }

  function & getValidator()
  {
    if(is_object($this->validator))
      return $this->validator;

    $this->validator =& $this->_init_validator();

    return $this->validator;
  }

  function setValidator(&$validator)
  {
    $this->validator =& $validator;
  }

  function _registerValidationRules(&$validator, &$dataspace)
  {
  }

  function _init_validator()
  {
    include_once(WACT_ROOT . '/validation/validator.inc.php');
    $this->validator = new Validator();

    return new Validator();
  }

  function & getFormComponent()
  {
    $toolkit =& Limb :: toolkit();
    $view =& $toolkit->getView();
    return $view->findChild($this->form_id);
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
}
?>
