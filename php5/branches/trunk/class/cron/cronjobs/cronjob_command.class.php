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
require_once(LIMB_DIR . '/class/core/commands/command.interface.php');
require_once(LIMB_DIR . '/class/core/request/nonbuffered_response.class.php');

class cronjob_command implements Command
{
  protected $response;

  function __construct()
  {
    $this->response = new nonbuffered_response();
  }

  public function set_response($response)
  {
    $this->response = $response;
  }

  public function get_response()
  {
    return $this->response;
  }

  public function perform()
  {
  }
}

?>