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

class SaveNewObjectAccessCommand implements Command
{
  function perform()
  {
    $toolkit = Limb :: toolkit();

    $dataspace = $toolkit->getDataspace();
    if (!$object = $dataspace->get('created_site_object'))
      throw new LimbException('can\'t find created site object in dataspace');

    $parent_id = $object->getParentNodeId();

    $datasource = $toolkit->getDatasource('SingleObjectDatasource');
    $datasource->setNodeId($parent_id);
    $parent_object = wrapWithSiteObject($datasource->fetch());

    $ctrl =& $parent_object->getController();
    $action = $ctrl->getRequestedAction($toolkit->getRequest());

    try
    {
      $access_policy = $this->_getAccessPolicy();
      $access_policy->saveNewObjectAccess($object, $parent_object, $action);
    }
    catch(LimbException $e)
    {
      return Limb :: STATUS_ERROR;
    }

    return Limb :: getSTATUS_OK();
  }

  // for mocking
  function _getAccessPolicy()
  {
    return new $access_policy;
  }
}


?>