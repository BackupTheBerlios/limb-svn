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

class SessionStartupFilter implements InterceptingFilter
{
  function run($filter_chain, $request, $response)
  {
    Debug :: addTimingPoint('session startup filter started');

    Limb :: toolkit()->getSession()->start();

    Debug :: addTimingPoint('session startup filter finished');

    $filter_chain->next();
  }
}
?>
