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

class RedirectCommand// implements Command
{
  var $path;

  function RedirectCommand($path)
  {
    $this->path = $path;
  }

  function perform()
  {
    $toolkit =& Limb :: toolkit();
    $response =& $toolkit->getResponse();

    $response->redirect($this->path);

    return LIMB_STATUS_OK;
  }
}


?>
