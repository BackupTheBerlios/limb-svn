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
require_once(LIMB_DIR . '/class/core/request/NonbufferedResponse.class.php');

class CronjobCommand //implements Command
{
  var $response;

  function CronjobCommand()
  {
    $this->response =& new NonbufferedResponse();
  }

  function setResponse(&$response)
  {
    $this->response =& $response;
  }

  function getResponse()
  {
    return $this->response;
  }

  function perform()
  {
  }
}

?>