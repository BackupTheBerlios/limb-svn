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

class SQLException extends LimbException
{
  var $_driver_error;

  function SQLException($message, $driver_error = null, $additional_params = array())
  {
    if ($driver_error !== null)
    {
      $this->_driver_error = $driver_error;
      $additional_params['driver_error'] = $driver_error;
    }

    parent :: LimbException($message, $additional_params);
  }

  function getDriverError()
  {
    return $this->_driver_error;
  }
}

?>