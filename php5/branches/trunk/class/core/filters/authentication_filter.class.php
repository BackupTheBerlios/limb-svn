<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/filters/intercepting_filter.interface.php');
require_once(LIMB_DIR . '/class/core/session.class.php');

class authentication_filter implements intercepting_filter
{
  public function run($filter_chain, $request, $response)
  {
    debug :: add_timing_point('authentication filter started');
    
    $this->_initialize_user();
    
    $toolkit = Limb :: toolkit();
    $datasource = $toolkit->createDatasource('single_object_datasource');
    $datasource->set_request($request);
    
    if(!$node = $datasource->map_request_to_node($request))
    {
      $this->_process_404_object($request, $response);
      $filter_chain->next();
      return;
    }
    
    $behaviour = $this->_get_behavoiur($node['object_id']);
    
    $controller = new site_object_controller($behaviour);
    if(!$action = $controller->get_requested_action())
    {
      $this->_process_404_object($request, $response);
      $filter_chain->next();
      return;
    }
    
    $datasource->set_id($node['object_id']);
    $datasource->set_permission_action($action);
    
    if(!$object_data = $datasource->fetch())
    {
  		$response->redirect('/root/login?redirect='. urlencode($_SERVER['REQUEST_URI']));
  		return;
    }

    $filter_chain->next();
  }
  
  protected function _initialize_user()
  {
    $user = Limb :: toolkit()->getUser();
    if($user->is_logged_in())
      return;
    
    $authenticator = Limb :: toolkit()->getAuthenticator();
    $authenticator->login(array('login' => '', 'password' => ''));
  }
  
  protected function _get_behavoiur($object_id)
  {
    $behaviour_name = site_object :: find_behaviour_name_by_id($object_id);
    return Limb :: toolkit()->createBehaviour($behaviour_name); 
  }

  protected function _process_404_object($request, $response)
  {  
    if(defined('ERROR_DOCUMENT_404'))
      $response->redirect(ERROR_DOCUMENT_404);
    else
      $response->header("HTTP/1.1 404 Not found");
  }    
}
?>