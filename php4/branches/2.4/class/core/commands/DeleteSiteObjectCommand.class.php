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
require_once(LIMB_DIR . '/class/core/commands/Command.interface.php');

class DeleteSiteObjectCommand implements Command
{
  protected function _getObjectToDelete()
  {
    $toolkit = Limb :: toolkit();
    $datasource = $toolkit->getDatasource('RequestedObjectDatasource');
    $datasource->setRequest($toolkit->getRequest());

    return wrapWithSiteObject($datasource->fetch());
  }

  public function perform()
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
      return Limb :: STATUS_ERROR;
    }

    return Limb :: STATUS_OK;
  }
}

?>
