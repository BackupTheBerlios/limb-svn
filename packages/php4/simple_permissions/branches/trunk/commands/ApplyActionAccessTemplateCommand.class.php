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

class ApplyActionAccessTemplateCommand// implements Command
{
  function perform()
  {
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();

    $datasource =& $toolkit->getDatasource('RequestedObjectDatasource');
    $datasource->setRequest($request);

    $object = wrapWithSiteObject($datasource->fetch());

    $ctrl =& $object->getController();
    $action = $ctrl->getRequestedAction($request);

    $access_policy =& $this->_getAccessPolicy();
    $access_policy->applyAccessTemplates($object, $action);

    if(catch('LimbException', $e))
      return LIMB_STATUS_ERROR;
    elseif(catch('Exception', $e))
      return throw($e);

    return LIMB_STATUS_OK;
  }

  // for mocking
  function &_getAccessPolicy()
  {
    return new $access_policy;
  }
}


?>