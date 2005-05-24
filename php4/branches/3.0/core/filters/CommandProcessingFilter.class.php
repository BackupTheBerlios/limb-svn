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

class CommandProcessingFilter//implements InterceptingFilter
{
  function run(&$filter_chain, &$request, &$response, &$context)
  {
    if(!$service =& $context->getObject('Service'))
      die('Service is not mapped!');//FIX

    $command =& $service->getActionCommand($service->getCurrentAction());
    $command->perform($context);

    $filter_chain->next();
  }
}

?>