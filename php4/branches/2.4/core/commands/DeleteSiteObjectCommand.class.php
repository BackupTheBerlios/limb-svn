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

class DeleteSiteObjectCommand// implements Command
{
  function _getObjectToDelete()
  {
    $toolkit = Limb :: toolkit();
    $dao = $toolkit->createDAO('RequestedObjectDAO');
    $dao->setRequest($toolkit->getRequest());

    return wrapWithSiteObject($dao->fetch());
  }

  function perform()
  {
    $object = $this->_getObjectToDelete();

    $object->delete();

    if(catch('SQLException', $e))
      return throw($e);
    elseif(catch('LimbException', $e))
      return LIMB_STATUS_ERROR;

    return LIMB_STATUS_OK;
  }
}

?>
