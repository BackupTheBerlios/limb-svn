<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/filters/intercepting_filter.class.php');
require_once(LIMB_DIR . '/core/lib/session/session.class.php');

class authentication_filter extends intercepting_filter
{
  function run(&$filter_chain, &$request, &$response)
  { 
    start_user_session();
    
    if(!$object_data = fetch_requested_object($request))
    {
      if(!$node = map_request_to_node($request))
      {
      	if(defined('ERROR_DOCUMENT_404'))
      		$response->redirect(ERROR_DOCUMENT_404);
      	else
      		$response->header("HTTP/1.1 404 Not found");
      	return;
      }
      
      
      $user =& user :: instance();
    	if (!$user->is_logged_in())
    	{
    		$tree = tree :: instance();
    		
    		$response->redirect('/root/login?redirect='. $tree->get_path_to_node($node));
    		return;
    	}	
    	else
    	{
    		debug :: write_error('content object not allowed or retrieved', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
    		        	
    		if(defined('ERROR_DOCUMENT_403'))
    			$response->redirect(ERROR_DOCUMENT_403);
    		else
    			$response->header("HTTP/1.1 403 Access denied");
    		return;
    	}	
    }
    
    $object =& wrap_with_site_object($object_data); 

    debug :: add_timing_point('object fetched');
    
    $site_object_controller =& $object->get_controller();
    
    if(($action = $site_object_controller->determine_action($request)) === false)
    {
    	debug :: write_error('"'. $action . '" action not found', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
    
    	if(defined('ERROR_DOCUMENT_404'))
    		$response->redirect(ERROR_DOCUMENT_404);
    	else
    		$response->header("HTTP/1.1 404 Not found");
    	
    	$filter_chain->next();
    	return;
    }
        
    $actions = $object->get_attribute('actions');
    
    if(!isset($actions[$action]))
    {
    	debug :: write_error('"'. $action . '" action is not accessible', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
    	
    	if (debug :: is_console_enabled())
    		echo debug :: parse_html_console();
    		
    	if(defined("ERROR_DOCUMENT_403"))
    		$response->redirect(ERROR_DOCUMENT_403);
    	else
    		$response->header("HTTP/1.1 403 Access denied");
    	return;
    }
            
    $filter_chain->next();        
  }
}
?>