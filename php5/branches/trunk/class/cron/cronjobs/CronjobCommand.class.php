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
require_once(LIMB_DIR . '/class/core/commands/Command.interface.php');
require_once(LIMB_DIR . '/class/core/request/NonbufferedResponse.class.php');

class CronjobCommand implements Command
{
  protected $response;

  function __construct()
  {
    $this->response = new NonbufferedResponse();
  }

  public function setResponse($response)
  {
    $this->response = $response;
  }

  public function getResponse()
  {
    return $this->response;
  }

  public function perform()
  {
  }
}

?>