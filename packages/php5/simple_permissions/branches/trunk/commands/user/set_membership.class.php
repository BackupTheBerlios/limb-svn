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
require_once(LIMB_DIR . '/class/core/actions/form_action.class.php');

class set_membership extends form_action
{
  protected function _define_dataspace_name()
  {
    return 'set_membership';
  }

  protected function _init_dataspace($request)
  {
    $datasource = Limb :: toolkit()->getDatasource('requested_object_datasource');
    $datasource->set_request($request);

    $object_data = $datasource->fetch();

    $object = Limb :: toolkit()->createSiteObject('user_object');

    $data['membership'] = $object->get_membership($object_data['id']);

    $this->dataspace->import($data);
  }

  protected function _valid_perform($request, $response)
  {
    $datasource = Limb :: toolkit()->getDatasource('requested_object_datasource');
    $datasource->set_request($request);

    $object_data = $datasource->fetch();

    $data = $this->dataspace->export();
    $object = Limb :: toolkit()->createSiteObject('user_object');

    $object->save_membership($object_data['id'], $data['membership']);

    $request->set_status(request :: STATUS_FORM_SUBMITTED);
  }

}

?>