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

class ActionMappingFilter//implements InterceptingFilter
{
  function run(&$filter_chain, &$request, &$response)
  {
    $toolkit =& Limb :: toolkit();
    $service =& $toolkit->getRequestResolver();

    if(!is_object($service))
      return;

    if(!$action = $request->get('action'))
    {
      $service->setCurrentAction($service->getDefaultAction());
    }
    elseif($service->actionExists($action))
    {
      $service->setCurrentAction($action);
    }
    else
    {
      $service404 = new Service('404');
      $service404->setCurrentAction($service404->getDefaultAction());
      $toolkit->setRequestResolver($service404);
    }

    $filter_chain->next();
  }
}

?>