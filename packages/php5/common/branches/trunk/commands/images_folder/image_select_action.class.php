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
require_once(LIMB_DIR . '/class/core/actions/action.class.php');

class image_select_action extends action
{
  public function perform($request, $response)
  {
    $datasource = Limb :: toolkit()->getDatasource('requested_object_datasource');
    $datasource->set_request($request);

    $object = $datasource->fetch();

    Limb :: toolkit()->getSession()->set('limb_image_select_working_path', $object['path']);
  }
}
?>