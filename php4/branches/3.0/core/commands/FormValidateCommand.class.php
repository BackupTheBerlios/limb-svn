<?php

class FormValidateCommand
{
  var $form_id;
  var $validator_handle;

  function FormValidateCommand($form_id, &$validator_handle)
  {
    $this->form_id = $form_id;
    $this->validator_handle =& $validator_handle;
  }

  function perform()
  {
    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->getDataspace();

    $validator =& Handle :: resolve($this->validator_handle);

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

  function _registerValidationRules(&$validator, &$dataspace)
  {
  }

  function & getFormComponent()
  {
    $toolkit =& Limb :: toolkit();
    $view =& $toolkit->getView();
    return $view->findChild($this->form_id);
  }
}
?>
