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

class InitializeExistingObjectCommand
{
  function InitializeExistingObjectCommand(){}

  function perform()
  {
    $toolkit =& Limb :: toolkit();
    $mapped_object =& $toolkit->getMappedObject();
    $toolkit->setProcessedObject($mapped_object);

    return LIMB_STATUS_OK;
  }
}

?>
