<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: EditSimpleObjectCommand.class.php 1186 2005-03-23 09:47:34Z seregalimb $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/services/Service.class.php');

class AttachServiceToObjectCommand
{
  var $name;

  function AttachServiceToObjectCommand($name)
  {
    $this->name = $name;
  }

  function perform()
  {
    $toolkit =& Limb :: toolkit();
    $object =& $toolkit->getProcessedObject();

    if(!is_a($object, 'Service'))
      return LIMB_STATUS_ERROR;

    $object->attachService(new Service($this->name));

    return LIMB_STATUS_OK;
  }
}

?>
