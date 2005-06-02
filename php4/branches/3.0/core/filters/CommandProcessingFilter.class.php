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

class CommandProcessingFilter//implements InterceptingFilter
{
  function run(&$filter_chain, &$request, &$response)
  {
    $toolkit =& Limb :: toolkit();
    if(!$service =& $toolkit->getService())
      die('Service is not mapped!:' . __FILE__ . ' at line ' . __LINE__);//FIX

    $command =& $service->getActionCommand($service->getCurrentAction());
    $command->perform();

    $filter_chain->next();
  }
}

?>