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
require_once(LIMB_DIR . '/class/core/filters/InterceptingFilter.interface.php');
require_once(LIMB_DIR . '/class/core/session/Session.class.php');

class AuthenticationFilter implements InterceptingFilter
{
  function run($filter_chain, $request, $response)
  {
    Debug :: addTimingPoint('authentication filter started');

    $this->initializeUser();

    $toolkit = Limb :: toolkit();
    $datasource = $toolkit->getDatasource('RequestedObjectDatasource');

    if(!$node = $datasource->mapRequestToNode($request))
    {
      $this->process404Error($request, $response);
      $filter_chain->next();
      return;
    }

    $behaviour = $this->getBehaviourByObjectId($node['object_id']);

    $controller = $this->_getController($behaviour);
    if(!$action = $controller->getRequestedAction())
    {
      $this->process404Error($request, $response);
      $filter_chain->next();
      return;
    }

    $datasource->setRequest($request);
    $datasource->setPermissionsAction($action);

    if(!$object_data = $datasource->fetch())
    {
      $response->redirect('/root/login?redirect='. urlencode($_SERVER['REQUEST_URI']));
      return;
    }

    $filter_chain->next();
  }

  // for mocking
  function _getController($behaviour)
  {
    return new SiteObjectController($behaviour);
  }

  function initializeUser()
  {
    $user = Limb :: toolkit()->getUser();
    if($user->isLoggedIn())
      return;

    $authenticator = Limb :: toolkit()->getAuthenticator();
    $authenticator->login(array('login' => '', 'password' => ''));
  }

  function getBehaviourByObjectId($object_id)
  {
    $behaviour_name = SiteObject :: findBehaviourNameById($object_id);
    return Limb :: toolkit()->createBehaviour($behaviour_name);
  }

  function process404Error($request, $response)
  {
    if($object_404_path = Limb :: toolkit()->getINI('common.ini')->getOption('404', 'ERROR_DOCUMENTS'))
      $response->redirect($object_404_path);
    else
      $response->header("HTTP/1.1 404 Not found");
  }
}
?>