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

class ServiceActionMappingFilter//implements InterceptingFilter
{
  function run(&$filter_chain, &$request, &$response, &$context)
  {
    $toolkit =& Limb :: toolkit();

    $action_resolver =& $toolkit->getRequestResolver('action');
    $service_resolver =& $toolkit->getRequestResolver('service');

    if(!is_object($service_resolver) || !is_object($action_resolver))
      return throw_error(new LimbException('request resolvers not set'));

    $service =& $service_resolver->resolve($request);
    if(!$action =& $action_resolver->resolve($request))
    {
      $toolkit->setService($service);
    }
    elseif($service->actionExists($action))
    {
      $service->setCurrentAction($action);
      $toolkit->setService($service);
    }
    else
    {
      $service404 = new Service('404');
      $toolkit->setService($service404);
    }

    $filter_chain->next();
  }
}

?>