<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
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
    debug :: add_timing_point('authentication filter started');

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
      $response->redirect('/root/login?redirect='. urlencode($_SERVER['REQUEST_URI']));
      return;
    }

    $object =& wrap_with_site_object($object_data);

    $site_object_controller =& $object->get_controller();

    if(($action = $site_object_controller->determine_action($request)) === false)
    {
      debug :: write_error('"'. $action . '" action not found', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);

      if(defined('ERROR_DOCUMENT_404'))
        $response->redirect(ERROR_DOCUMENT_404);
      else
        $response->header("HTTP/1.1 404 Not found");

      debug :: add_timing_point('authentication filter finished');

      $filter_chain->next();
      return;
    }

    $actions = $object->get_attribute('actions');

    if(!isset($actions[$action]))
    {
      $redirect_path = $site_object_controller->get_action_property($action, 'inaccessible_redirect');
      $redirect_type = $site_object_controller->get_action_property($action, 'redirect_type');
      $redirect_template_path = $site_object_controller->get_action_property($action, 'redirect_template_path');
      
      if(!$redirect)
        $redirect = '/root/login';
      
      $response->redirect($redirect_path . '?redirect='. urlencode($_SERVER['REQUEST_URI']), $redirect_type, $redirect_template_path);
    }

    debug :: add_timing_point('authentication filter finished');

    $filter_chain->next();
  }
}
?>
