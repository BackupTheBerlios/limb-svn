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

    try
    {
      $object->delete();
    }
    catch (SQLException $sql_e)
    {
      throw $sql_e;
    }
    catch(LimbException $e)
    {
      return LIMB_STATUS_ERROR;
    }

    return Limb :: getSTATUS_OK();
  }
}

?>
