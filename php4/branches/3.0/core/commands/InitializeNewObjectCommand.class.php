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

class InitializeNewObjectCommand
{
  var $handle;
  function InitializeNewObjectCommand(&$handle)
  {
    $this->handle =& $handle;
  }

  function perform()
  {
    $toolkit =& Limb :: toolkit();

    $object =& Handle :: resolve($this->handle);

    $toolkit->setProcessedObject($object);

    return LIMB_STATUS_OK;
  }
}

?>
