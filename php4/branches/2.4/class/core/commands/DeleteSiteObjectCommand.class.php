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
    $datasource = $toolkit->getDatasource('RequestedObjectDatasource');
    $datasource->setRequest($toolkit->getRequest());

    return wrapWithSiteObject($datasource->fetch());
  }

  function perform()
  {
    $object = $this->_getObjectToDelete();

    if(Limb :: isError($res = $object->delete()))
    {
      if(is_a($res, 'SQLException'))
        return $res;
      elseif(is_a($res, 'LimbException'))
        return LIMB_STATUS_ERROR;
    }

    return LIMB_STATUS_OK;
  }
}

?>
