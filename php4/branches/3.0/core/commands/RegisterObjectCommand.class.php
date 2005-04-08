<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: CreateSimpleObjectCommand.class.php 1165 2005-03-16 14:28:14Z pachanga $
*
***********************************************************************************/

class RegisterObjectCommand
{
  var $field_name;

  function RegisterObjectCommand($field_name)
  {
    $this->field_name = $field_name;
  }

  function perform(&$context)
  {
    if(!$object =& $context->getObject($this->field_name))
      return LIMB_STATUS_ERROR;

    $toolkit =& Limb :: toolkit();
    $uow =& $toolkit->getUOW();

    $uow->register($object);

    return LIMB_STATUS_OK;
  }
}

?>
