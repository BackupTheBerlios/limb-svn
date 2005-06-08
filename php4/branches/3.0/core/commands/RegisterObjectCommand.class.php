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

    $uow->registerNew($this->object);

    return LIMB_STATUS_OK;
  }
}

?>
