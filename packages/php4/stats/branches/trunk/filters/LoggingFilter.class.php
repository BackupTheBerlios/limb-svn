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

class LoggingFilter// implements InterceptingFilter
{
  function run($filter_chain, $request, $response)
  {
    $filter_chain->next();

    Debug :: addTimingPoint('logging filter started');

    $toolkit =& Limb :: toolkit();
    $datasource =& $toolkit->getDatasource('RequestedObjectDatasource');
    $datasource->setRequest($request);

    $object = wrapWithSiteObject($datasource->fetch());

    $controller = $object->getController();

    include_once(dirname(__FIlE__) . '/../StatsRegister.class.php');

    $stats_register = new StatsRegister();

    $stats_register->register(
      $object->getNodeId(),
      $controller->getAction($request),
      $request->getStatus()
    );

    Debug :: addTimingPoint('logging filter finished');
  }
}
?>