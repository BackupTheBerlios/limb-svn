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

class ImageSelectAction extends Action
{
  public function perform($request, $response)
  {
    $datasource = Limb :: toolkit()->getDatasource('RequestedObjectDatasource');
    $datasource->setRequest($request);

    $object = $datasource->fetch();

    Limb :: toolkit()->getSession()->set('limb_image_select_working_path', $object['path']);
  }
}
?>