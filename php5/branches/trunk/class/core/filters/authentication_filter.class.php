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
require_once(LIMB_DIR . '/class/core/session/session.class.php');

class authentication_filter implements intercepting_filter
{
  public function run($filter_chain, $request, $response)
  {
    debug :: add_timing_point('authentication filter started');
    
    $this->initialize_user();
    
    $toolkit = Limb :: toolkit();
    $datasource = $toolkit->getDatasource('requested_object_datasource');
    
    if(!$node = $datasource->map_request_to_node($request))
    {
      $this->process_404_error($request, $response);
      $filter_chain->next();
      return;
    }
    
    $behaviour = $this->get_behaviour_by_object_id($node['object_id']);
    
    $controller = $this->_get_controller($behaviour);
    if(!$action = $controller->get_requested_action())
    {
      $this->process_404_error($request, $response);
      $filter_chain->next();
      return;
    }
    
    $datasource->set_request($request);
    $datasource->set_permissions_action($action);
    
    if(!$object_data = $datasource->fetch())
    {
  		$response->redirect('/root/login?redirect='. urlencode($_SERVER['REQUEST_URI']));
  		return;
    }

    $filter_chain->next();
  }
  
  // for mocking
  protected function _get_controller($behaviour)
  {
    return new site_object_controller($behaviour);
  }
  
  public function initialize_user()
  {
    $user = Limb :: toolkit()->getUser();
    if($user->is_logged_in())
      return;
    
    $authenticator = Limb :: toolkit()->getAuthenticator();
    $authenticator->login(array('login' => '', 'password' => ''));
  }
  
  public function get_behaviour_by_object_id($object_id)
  {
    $behaviour_name = site_object :: find_behaviour_name_by_id($object_id);
    return Limb :: toolkit()->createBehaviour($behaviour_name); 
  }

  public function process_404_error($request, $response)
  { 
    if($object_404_path = Limb :: toolkit()->getINI('common.ini')->get_option('404', 'ERROR_DOCUMENTS'))
      $response->redirect($object_404_path);
    else
      $response->header("HTTP/1.1 404 Not found");
  }    
}
?>