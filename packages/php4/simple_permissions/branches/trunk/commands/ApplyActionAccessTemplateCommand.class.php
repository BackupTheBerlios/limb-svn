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
  public function perform()
  {
    $toolkit = Limb :: toolkit();
    $request = $toolkit->getRequest();

    $datasource = $toolkit->getDatasource('RequestedObjectDatasource');
    $datasource->setRequest($request);

    $object = wrapWithSiteObject($datasource->fetch());

    $action = $object->getController()->getRequestedAction($request);

    try
    {
      $access_policy = $this->_getAccessPolicy();
      $access_policy->applyAccessTemplates($object, $action);
    }
    catch(LimbException $e)
    {
      return Limb :: STATUS_ERROR;
    }

    return Limb :: STATUS_OK;
  }

  // for mocking
  protected function _getAccessPolicy()
  {
    return new $access_policy;
  }
}


?>