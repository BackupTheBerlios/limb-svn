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
  var $object_handle;

  function InitializeExistingObjectCommand(&$object_handle)
  {
    $this->object_handle =& $object_handle;
  }

  function perform()
  {
    $object =& Handle :: resolve($this->object_handle);

    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $uow =& $toolkit->getUOW();
    if(!$object = $uow->load($object->__class_name, $request->get('id')))
      return LIMB_STATUS_ERROR;

    $toolkit->setProcessedObject($object);

    return LIMB_STATUS_OK;
  }
}

?>
