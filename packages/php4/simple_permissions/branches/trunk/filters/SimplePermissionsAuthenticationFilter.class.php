<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: authentication_filter.class.php 814 2004-10-21 12:46:23Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/filters/AuthenticationFilter.class.php');

class SimplePermissionsAuthenticationFilter extends AuthenticationFilter
{
  function run(&$filter_chain, &$request, &$response)
  {
    Debug :: addTimingPoint('authentication filter started');

    $this->initializeUser();

    $toolkit =& Limb :: toolkit();
    $datasource =& $toolkit->getDatasource('RequestedObjectDatasource');

    if(!$node = $datasource->mapRequestToNode($request))
    {
      $this->process404Error($request, $response);
      $filter_chain->next();
      return;
    }

    $behaviour =& $this->getBehaviourByObjectId($node['object_id']);

    $controller =& $this->_getController($behaviour);
    if(!$action = $controller->getRequestedAction())
    {
      $this->process404Error($request, $response);
      $filter_chain->next();
      return;
    }

    $datasource->setRequest($request);

    if(!$object_data = $datasource->fetch())
    {
      $response->redirect('/root/login?redirect='. urlencode($_SERVER['REQUEST_URI']));
      return;
    }

    $toolkit =& Limb :: toolkit();
    $authoriser =& $toolkit->getAuthorizer();
    $authoriser->assignActionsToObjects($object_data);

    if (!isset($object_data['actions']) ||  !isset($object_data['actions'][$action]))
    {
      $response->redirect('/root/login?redirect='. urlencode($_SERVER['REQUEST_URI']));
      return;
    }

    $filter_chain->next();
  }
}
?>