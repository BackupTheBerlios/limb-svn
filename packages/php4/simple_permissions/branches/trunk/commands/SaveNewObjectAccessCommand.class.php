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
      return new LimbException('can\'t find created site object in dataspace');

    $parent_id = $object->getParentNodeId();

    $datasource =& $toolkit->getDatasource('SingleObjectDatasource');
    $datasource->setNodeId($parent_id);
    $parent_object = wrapWithSiteObject($datasource->fetch());

    $ctrl =& $parent_object->getController();
    $action = $ctrl->getRequestedAction($toolkit->getRequest());

    $access_policy =& $this->_getAccessPolicy();
    if(!Limb :: isError($e = $access_policy->saveNewObjectAccess($object, $parent_object, $action)))
      return LIMB_STATUS_OK;

    if(is_a($e, 'LimbException'))
      return LIMB_STATUS_ERROR;
    else
      return $e;
  }

  // for mocking
  function _getAccessPolicy()
  {
    return new $access_policy;
  }
}


?>