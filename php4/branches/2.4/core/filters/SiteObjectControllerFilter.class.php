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

class SiteObjectControllerFilter// implements InterceptingFilter
{
  function run(&$filter_chain, &$request, &$response)
  {
    Debug :: addTimingPoint('site object controller filter started');

    $toolkit =& Limb :: toolkit();
    $dao =& $toolkit->createDAO('RequestedObjectDAO');
    $dao->setRequest($request);

    $site_object = wrapWithSiteObject($dao->fetch());
    $ctrlr =& $site_object->getController();
    $ctrlr->process($request);

    Debug :: addTimingPoint('site object controller filter finished');

    $filter_chain->next();
  }

  function _getController($behaviour)
  {
    return new SiteObjectController($behaviour);
  }
}
?>