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
class Rule
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

  function error($error, $params=array()){die('abstract function!')}

  function validate($dataspace){die('abstract function!')}
}

?>