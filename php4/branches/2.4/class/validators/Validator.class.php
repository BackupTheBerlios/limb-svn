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
require_once(LIMB_DIR . '/class/validators/ErrorList.class.php');

class Validator
{
  var $rules = array();

  var $is_valid = true;

  function addRule(&$rule)
  {
    $this->rules[] =& $rule;
  }

  function &_getErrorList()
  {
    return ErrorList :: instance();
  }

  function isValid()
  {
    return $this->is_valid;
  }

  function validate(&$dataspace)
  {
    foreach($this->rules as $key => $rule)
    {
      resolveHandle($this->rules[$key]);

      $this->rules[$key]->setErrorList($this->_getErrorList());
      $this->rules[$key]->validate($dataspace);
      $this->is_valid = (bool)($this->is_valid & $this->rules[$key]->isValid());
    }
    return $this->is_valid;
  }

  function & getRules()
  {
    return $this->rules;
  }

  function addError($field_name, $error, $params=array())
  {
    $error_list = $this->_getErrorList();
    $error_list->addError($field_name, $error, $params);
  }
}

?>