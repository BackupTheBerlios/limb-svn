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

class SiteObjectControllerFilter implements InterceptingFilter
{
  function run($filter_chain, $request, $response)
  {
    Debug :: addTimingPoint('site object controller filter started');

    $datasource = Limb :: toolkit()->getDatasource('RequestedObjectDatasource');
    $datasource->setRequest($request);

    $site_object = wrapWithSiteObject($datasource->fetch());

    $site_object->getController()->process($request);

    Debug :: addTimingPoint('site object controller filter finished');

    $filter_chain->next();
  }

  function _getController($behaviour)
  {
    return new SiteObjectController($behaviour);
  }
}
?>