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
require_once(LIMB_DIR . '/class/core/actions/Action.class.php');

class NodeSelectAction extends Action
{
  public function perform($request, $response)
  {
    $request->setStatus(Request :: STATUS_DONT_TRACK);

    if(!$path = $request->get('path'))
      return;

    $datasource = Limb :: toolkit()->getDatasource('SingleObjectDatasource');
    $datasource->setPath($path);

    if(!$object_data = $datasource->fetch())
      return;

    Limb :: toolkit()->getSession()->set('limb_node_select_working_path', $path);
    $dataspace = $this->view->findChild('parent_node_data');

    $dataspace->import($object_data);
  }
}

?>