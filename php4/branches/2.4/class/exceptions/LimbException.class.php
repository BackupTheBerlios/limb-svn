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

class LimbException extends Exception
{
  var $_additional_params = array();

  function LimbException($message, $params = array())
  {
    parent :: Exception($message);

    if (is_array($params) &&  sizeof($params))
      $this->_setAdditionalParams($params);
  }

  function _setAdditionalParams($params)
  {
    $this->_additional_params = $params;
  }

  function getAdditionalParams()
  {
    return $this->_additional_params;
  }

  function __toString()
  {
    $str =  __CLASS__ . " : \"{$this->message}\"\n";
    $str .= "[params: " . var_export($this->_additional_params, true) . "]\n";

    $str .= $this->getTraceAsString();

    return $str;
  }
}

?>