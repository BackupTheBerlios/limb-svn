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
  function perform($request, $response)
  {
    $t =& Limb :: toolkit();
    $datasource =& $t->getDatasource('RequestedObjectDatasource');
    $datasource->setRequest($request);

    $object = $datasource->fetch();

    $t->getSession()->set('limb_image_select_working_path', $object['path']);
  }
}
?>