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

class logging_filter extends intercepting_filter
{
    function run(&$filter_chain, &$request, &$response)
    {
      $filter_chain->next();

      debug :: add_timing_point('logging filter started');

      $object = wrap_with_site_object(fetch_requested_object($request));

      $controller = $object->get_controller();

      include_once(LIMB_DIR . '/core/model/stats/stats_register.class.php');

      $stats_register = new stats_register();

      $controller->determine_action();

      $stats_register->register(
        $object->get_node_id(),
        $controller->get_action(),
        $request->get_status());

      debug :: add_timing_point('logging filter finished');

    }
}
?>