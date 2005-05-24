<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: CrudMainBehaviour.class.php 23 2005-02-26 18:11:24Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/commands/PageRenderingCommand.class.php');

class UIHandleDialogCommand extends PageRenderingCommand
{
  function UIHandleDialogCommand()
  {
    parent :: PageRenderingCommand('/dialog_handle.html');
  }
}

?>