<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: InterceptingFilter.interface.php 981 2004-12-21 15:51:00Z pachanga $
*
***********************************************************************************/

class TimingFilter//implements InterceptingFilter
{
  function run(&$filter_chain, &$request, &$response, &$context)
  {
    $start_time = $this->getMicroTime();

    $filter_chain->next();

    echo '<small>' . round($this->getMicroTime() - $start_time, 2) . '</small>';
  }

  function getMicroTime()
  {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
  }
}

?>