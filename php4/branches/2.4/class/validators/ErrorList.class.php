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
/**
* Container for errors implementing the Iterator iterface
*
* @todo documention - check that err object is validation_error
*/
class ErrorList
{
  static protected $instance = null;

  protected $errors = array();

  static public function instance()
  {
    if (!self :: $instance)
      self :: $instance = new ErrorList();

    return self :: $instance;
  }

  public function addError($field_name, $error_msg, $params=array())
  {
    $this->errors[$field_name][] = array('error' => $error_msg, 'params' => $params);
  }

  public function getErrors($field_name)
  {
    if(isset($this->errors[$field_name]))
      return $this->errors[$field_name];
  }

  public function reset()
  {
    $this->errors = array();
  }

  public function export()
  {
    return $this->errors;
  }
}

?>