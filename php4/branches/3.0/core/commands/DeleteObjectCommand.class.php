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

class DeleteObjectCommand
{
  function DeleteObjectCommand(){}

  function perform()
  {
    $toolkit =& Limb :: toolkit();
    if(!$object =& $toolkit->getProcessedObject())
      return LIMB_STATUS_ERROR;

    $uow =& $toolkit->getUOW();

    $uow->delete($object);

    return LIMB_STATUS_OK;
  }
}

?>
