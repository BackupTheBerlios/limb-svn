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
require_once(LIMB_DIR . '/class/core/fetcher.class.php');

class site_object_controller_filter implements intercepting_filter
{
  public function run($filter_chain, $request, $response)
  {
    debug :: add_timing_point('site object controller filter started');

    $site_object = wrap_with_site_object(LimbToolsBox :: getToolkit()->getFetcher()->fetch_requested_object($request));

    $site_object_controller = $site_object->get_controller();

    $site_object_controller->get_action($request);

    $site_object_controller->process($request, $response);

    if($response->is_empty())
      $site_object_controller->display_view();

    debug :: add_timing_point('site object controller filter finished');

    $filter_chain->next();
  }
}
?>