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
    
    if(!$object_data = Limb :: toolkit()->getFetcher()->fetch_requested_object($request))
    {
      if(!$node = Limb :: toolkit()->getFetcher()->map_request_to_node($request))
      {
      	if(defined('ERROR_DOCUMENT_404'))
      		$response->redirect(ERROR_DOCUMENT_404);
      	else
      		$response->header("HTTP/1.1 404 Not found");
      	return;
      }
  		$response->redirect('/root/login?redirect='. urlencode($_SERVER['REQUEST_URI']));
  		return;
    }

    $object = wrap_with_site_object($object_data);

    $site_object_controller = $object->get_controller();

    try
    {
      $action = $site_object_controller->get_action($request);
    }
    catch(LimbException $e)
    {
    	debug :: write_exception($e);

    	if(defined('ERROR_DOCUMENT_404'))
    		$response->redirect(ERROR_DOCUMENT_404);
    	else
    		$response->header("HTTP/1.1 404 Not found");

      debug :: add_timing_point('authentication filter finished');

    	$filter_chain->next();
    	return;
    }

    $actions = $object->get('actions');

    if(!isset($actions[$action]))
    {
      $response->redirect('/root/login?redirect='. urlencode($_SERVER['REQUEST_URI']));
    }

    debug :: add_timing_point('authentication filter finished');

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
}
?>