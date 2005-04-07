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
  var $object;

  function RegisterObjectCommand(&$object)
  {
    $this->object =& $object;
  }

  function perform()
  {
    $toolkit =& Limb :: toolkit();
    $uow =& $toolkit->getUOW();

    $uow->register($this->object);

    return LIMB_STATUS_OK;
  }
}

?>
