<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: InterceptingFilter.interface.php 981 2004-12-21 15:51:00Z pachanga $
*
***********************************************************************************/

class SimpleACLAccessFilter //implements InterceptingFilter
{
  function run(&$filter_chain, &$request, &$response, &$context)
  {
    if(!$service =& $context->getObject('Service'))
    {
      $filter_chain->next();
      return;
    }

    $service_name = $service->getName();
    $action = $service->getCurrentAction();

    $uri =& $request->getUri();

    $path =& $uri->getPath();

    $acl_toolkit =& Limb :: toolkit('SimpleACL');
    $authorizer =& $acl_toolkit->getAuthorizer();
    if($authorizer->canDo($action, $path, $service_name))
    {
      $filter_chain->next();
      return;
    }

    $new_service = new Service('403');
    $new_service->setCurrentAction($new_service->getDefaultAction());
    $context->setObject('Service', $new_service);
    $filter_chain->next();
  }
}

?>