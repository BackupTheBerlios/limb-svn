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

class ErrorList
{
  var $errors = array();

  function & instance()
  {
    if (!isset($GLOBALS['ErrorListGlobalInstance']) || !is_a($GLOBALS['ErrorListGlobalInstance'], 'ErrorList'))
      $GLOBALS['ErrorListGlobalInstance'] =& new ErrorList();

    return $GLOBALS['ErrorListGlobalInstance'];
  }

  function addError($field_name, $error_msg, $params=array())
  {
    $this->errors[$field_name][] = array('error' => $error_msg, 'params' => $params);
  }

  function getErrors($field_name)
  {
    if(isset($this->errors[$field_name]))
      return $this->errors[$field_name];
  }

  function reset()
  {
    $this->errors = array();
  }

  function export()
  {
    return $this->errors;
  }
}

?>