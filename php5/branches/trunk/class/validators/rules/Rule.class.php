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
abstract class rule
{
  protected $error_list = null;

  protected $is_valid = true;

  public function is_valid()
  {
    return $this->is_valid;
  }

  public function set_error_list($error_list)
  {
    $this->error_list = $error_list;
  }

  abstract protected function error($error, $params=array());

  abstract public function validate($dataspace);
}

?>