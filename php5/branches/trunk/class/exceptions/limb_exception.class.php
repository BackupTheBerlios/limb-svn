<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
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
    if (is_array($params))
      $this->_setAdditionalParams($params);
  
    parent::__construct($message);
  }
  
  protected function _setAdditionalParams($params)
  {
    $this->_additional_params = $params;
    $this->message .= " [params: " . var_export($params, true) . "]";
  }
  
  public function getAdditionalParams()
  {
    return $this->_additional_params;
  }  
    
}

?>