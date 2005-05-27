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

class UIDialogFilter//implements InterceptingFilter
{
  function run(&$filter_chain, &$request, &$response)
  {
    $toolkit =& Limb :: toolkit();

    if(!$request->get('from_dialog'))
    {
      $filter_chain->next();
      return;
    }

    if(!$service = $toolkit->getService())
    {
      $filter_chain->next();
      return;
    }

    if($service->getName() == '404')
      $toolkit->setService( new Service('UIHandleDialog'));

    $filter_chain->next();
  }
}

?>