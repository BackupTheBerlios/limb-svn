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

class ApplyActionAccessTemplateCommand implements Command
{
  function perform()
  {
    $toolkit = Limb :: toolkit();
    $request = $toolkit->getRequest();

    $datasource = $toolkit->getDatasource('RequestedObjectDatasource');
    $datasource->setRequest($request);

    $object = wrapWithSiteObject($datasource->fetch());

    $ctrl =& $object->getController();
    $action = $ctrl->getRequestedAction($request);

    try
    {
      $access_policy = $this->_getAccessPolicy();
      $access_policy->applyAccessTemplates($object, $action);
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