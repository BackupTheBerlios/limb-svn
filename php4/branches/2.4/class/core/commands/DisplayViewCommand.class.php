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

class DisplayViewCommand implements Command
{
  function perform()
  {
    $toolkit =& Limb :: toolkit();

    if(!$view =& $toolkit->getView())
      throw new LimbException('view is null');

    ob_start();

    $view->display();

    $response =& $toolkit->getResponse();
    $response->write(ob_get_contents());

    if(ob_get_level())
      ob_end_clean();

    return Limb :: getSTATUS_OK();
  }
}

?>
