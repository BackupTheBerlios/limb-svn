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
  var $error_list = null;

  var $is_valid = true;

  function isValid()
  {
    return $this->is_valid;
  }

  function setErrorList($error_list)
  {
    $this->error_list = $error_list;
  }

  abstract function error($error, $params=array());

  abstract function validate($dataspace);
}

?>