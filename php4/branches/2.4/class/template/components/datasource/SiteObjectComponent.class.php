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
require_once(WACT_ROOT . '/template/template.inc.php');

class SiteObjectComponent extends Component
{
  function fetchByPath($path)
  {
    $toolkit =& Limb :: toolkit();
    $datasource =& $toolkit->getDatasource('SingleObjectDatasource');
    $datasource->setPath($path);
    $this->import($datasource->fetch());
  }

  function fetchRequested()
  {
    $toolkit =& Limb :: toolkit();
    $datasource =& $toolkit->getDatasource('RequestedObjectDatasource');
    $request =& $toolkit->getRequest();
    $datasource->setRequest($request);
    $this->import($datasource->fetch());
  }
}

?>