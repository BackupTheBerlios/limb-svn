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

class SaveNewObjectAccessCommand// implements Command
{
  function perform()
  {
    $toolkit = Limb :: toolkit();

    $dataspace = $toolkit->getDataspace();
    if (!$object = $dataspace->get('created_site_object'))
      return throw(new LimbException('can\'t find created site object in dataspace'));

    $parent_id = $object->getParentNodeId();

    $datasource =& $toolkit->getDatasource('SingleObjectDatasource');
    $datasource->setNodeId($parent_id);
    $parent_object = wrapWithSiteObject($datasource->fetch());

    $ctrl =& $parent_object->getController();
    $action = $ctrl->getRequestedAction($toolkit->getRequest());

    $access_policy =& $this->_getAccessPolicy();
    $access_policy->saveNewObjectAccess($object, $parent_object, $action);

    if(catch('LimbException', $e))
      return LIMB_STATUS_ERROR;
    elseif(catch('Exception', $e))
      return throw($e);

    return LIMB_STATUS_OK;
  }

  // for mocking
  function _getAccessPolicy()
  {
    return new $access_policy;
  }
}


?>