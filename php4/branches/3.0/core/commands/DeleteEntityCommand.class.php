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

class DeleteEntityCommand
{
  var $entity;

  function DeleteEntityCommand(&$entity)
  {
    $this->entity =& $entity;
  }

  function perform()
  {
    $toolkit =& Limb :: toolkit();
    $uow =& $toolkit->getUOW();
    $uow->delete($this->entity);

    return LIMB_STATUS_OK;
  }
}

?>
