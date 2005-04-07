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
  function run(&$filter_chain, &$request, &$response)
  {
    $toolkit =& Limb :: toolkit();
    $resolver =& $toolkit->getRequestResolver();

    if(!is_object($resolver))
      return throw(new Exception('request resolver not set'));

    $service =& $resolver->getRequestedService($request);
    if(!$action =& $resolver->getRequestedAction($request))
    {
      $service->setCurrentAction($service->getDefaultAction());
      $toolkit->setCurrentService($service);
    }
    elseif($service->actionExists($action))
    {
      $service->setCurrentAction($action);
      $toolkit->setCurrentService($service);
    }
    else
    {
      $service404 = new Service('404');
      $service404->setCurrentAction($service404->getDefaultAction());
      $toolkit->setCurrentService($service404);
    }

    $toolkit->setCurrentEntity($resolver->getRequestedEntity($request));

    $filter_chain->next();
  }
}

?>