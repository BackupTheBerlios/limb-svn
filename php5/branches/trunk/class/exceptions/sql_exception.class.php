<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
 
class SQLException extends LimbException 
{    
  protected $_driver_error;
  
  public function __construct($message, $driver_error = null, $additional_params = array())
  {
    if ($driver_error !== null)
    {
      $this->_driver_error = $driver_error;      
      $additional_params['driver_error'] = $driver_error;
    }
        
    parent::__construct($message, $additional_params);
  }
        
  public function getDriverError()
  {
    return $this->_driver_error;
  } 
}

?>