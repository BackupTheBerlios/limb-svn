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
require_once(LIMB_DIR . '/core/Object.class.php');

class DomainObject extends Object
{
  function DomainObject()
  {
    parent :: Object();

    $toolkit =& Limb :: toolkit();
    $uow =& $toolkit->getUOW();
    $uow->registerNew($this);
  }
}

?>