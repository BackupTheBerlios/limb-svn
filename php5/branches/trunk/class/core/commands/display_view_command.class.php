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

class display_view_command implements Command
{
  public function perform()
  {
    if(!$view = Limb :: toolkit()->getView())
      throw new LimbException('view is null');


    ob_start();

    $view->display();

    Limb :: toolkit()->getResponse()->write(ob_get_contents());

    if(ob_get_level())
      ob_end_clean();

    return Limb :: STATUS_OK;
  }
}

?>
