<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: StateMachine.class.php 1098 2005-02-10 12:06:14Z pachanga $
*
***********************************************************************************/

class NullCommand // implements Command
{
  function perform()
  {
    return LIMB_STATUS_OK;
  }
}

?>