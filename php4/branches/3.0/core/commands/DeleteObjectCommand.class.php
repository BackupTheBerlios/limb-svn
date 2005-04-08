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
  var $context_field;

  function DeleteObjectCommand($context_field)
  {
    $this->context_field = $context_field;
  }

  function perform(&$context)
  {
    if(!$object =& $context->getObject($this->context_field))
      return LIMB_STATUS_ERROR;

    $toolkit =& Limb :: toolkit();
    $uow =& $toolkit->getUOW();

    $uow->delete($object);

    return LIMB_STATUS_OK;
  }
}

?>
