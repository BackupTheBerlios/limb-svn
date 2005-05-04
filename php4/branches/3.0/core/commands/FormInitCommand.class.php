<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: FormInitCommand.class.php 1215 2005-04-12 14:35:01Z seregalimb $
*
***********************************************************************************/
define('LIMB_ANY_FORM_WILDCARD', '*');
define('LIMB_MULTI_FORM', true);
define('LIMB_SINGLE_FORM', false);

require_once(LIMB_DIR . '/core/util/ComplexArray.class.php');

class FormInitCommand// implements Command
{
  var $form_id;
  var $is_multi;

  function FormInitCommand($form_id, $is_multi = LIMB_SINGLE_FORM)
  {
    $this->form_id = $form_id;
    $this->is_multi = $is_multi;
  }

  function perform()
  {
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();

    $dataspace =& $this->_switchDataSpace();
    $form =& $this->getFormComponent();

    $form->registerDataSource($dataspace);

    if($this->_isFirstTime($request))
      return LIMB_STATUS_FORM_DISPLAYED;
    else
    {
      $dataspace->merge($this->_getRequestData($request));
      return LIMB_STATUS_FORM_SUBMITTED;
    }
  }

  function _isFirstTime(&$request)
  {
    $arr = $this->_getRequestData($request);
    if(isset($arr['submitted']) &&  $arr['submitted'])
      return false;
    else
      return true;
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

  function & getFormComponent()
  {
    $toolkit =& Limb :: toolkit();
    $view =& $toolkit->getView();
    $form =& $view->findChild($this->form_id);

    if(!is_object($form))
      die('Form component ' . $this->form_id . ' not found in '. $view->file);

    return $form;
  }
}


?>
