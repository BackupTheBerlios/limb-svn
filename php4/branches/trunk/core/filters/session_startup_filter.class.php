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

class session_startup_filter extends intercepting_filter
{
  function run(&$filter_chain, &$request, &$response)
  {
    debug :: add_timing_point('session startup filter started');

    require_once(LIMB_DIR . '/core/lib/session/session.class.php');
    start_user_session();

    debug :: add_timing_point('session startup filter finished');

    $filter_chain->next();
  }
}
?>