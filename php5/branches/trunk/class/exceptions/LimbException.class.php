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
  protected $_additional_params = array();

  public function __construct($message, $params = array())
  {
    parent::__construct($message);

    if (is_array($params) &&  sizeof($params))
      $this->_setAdditionalParams($params);
  }

  protected function _setAdditionalParams($params)
  {
    $this->_additional_params = $params;
  }

  public function getAdditionalParams()
  {
    return $this->_additional_params;
  }

  public function __toString()
  {
    $str =  __CLASS__ . " : \"{$this->message}\"\n";
    $str .= "[params: " . var_export($this->_additional_params, true) . "]\n";

    $str .= $this->getTraceAsString();

    return $str;
  }
}

?>