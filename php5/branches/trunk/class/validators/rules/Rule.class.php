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
abstract class Rule
{
  protected $error_list = null;

  protected $is_valid = true;

  public function isValid()
  {
    return $this->is_valid;
  }

  public function setErrorList($error_list)
  {
    $this->error_list = $error_list;
  }

  abstract protected function error($error, $params=array());

  abstract public function validate($dataspace);
}

?>