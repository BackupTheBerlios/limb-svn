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
require_once(LIMB_DIR . '/class/template/Component.class.php');

class SiteObjectComponent extends Component
{
  public function fetchByPath($path)
  {
    $datasource = Limb :: toolkit()->getDatasource('SingleObjectDatasource');
    $datasource->setPath($path);
    $this->import($datasource->fetch());
  }

  public function fetchRequested()
  {
    $datasource = Limb :: toolkit()->getDatasource('RequestedObjectDatasource');
    $request = Limb :: toolkit()->getRequest();
    $datasource->setRequest($request);
    $this->import($datasource->fetch());
  }
}

?>