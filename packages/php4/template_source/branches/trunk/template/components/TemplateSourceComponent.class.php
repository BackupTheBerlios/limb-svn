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
class TemplateSourceComponent extends Component
{
  function getCurrentTemplateSourceLink()
  {
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();

    $datasource =& $toolkit->getDatasource('RequestedObjectDatasource');
    $datasource->setRequest($request);

    if(!$site_object = wrapWithSiteObject($datasource->fetch()))
      return '';

    $site_object_controller = $site_object->getController();

    if(($action = $site_object_controller->getAction($request)) === false)
      return '';

    if(!$template_path = $site_object_controller->getActionProperty($action, 'template_path'))
      return '';

    return '/root/template_source?t[]=' . $template_path;
  }
}

?>