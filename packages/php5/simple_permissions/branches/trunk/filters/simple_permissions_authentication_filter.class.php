<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: authentication_filter.class.php 814 2004-10-21 12:46:23Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/filters/authentication_filter.class.php');

class simple_permissions_authentication_filter extends authentication_filter
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
    
    if(!$object_data = $datasource->fetch())
    {
  		$response->redirect('/root/login?redirect='. urlencode($_SERVER['REQUEST_URI']));
  		return;
    }
    
    $authoriser = Limb :: toolkit()->getAuthorizer();
    $authoriser->assign_actions_to_objects($object_data);
    
    if (!isset($object_data['actions']) || !isset($object_data['actions'][$action]))
    {
  		$response->redirect('/root/login?redirect='. urlencode($_SERVER['REQUEST_URI']));
  		return;
    }

    $filter_chain->next();
  }
}
?>