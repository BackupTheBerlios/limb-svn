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
require_once(LIMB_DIR . '/class/core/actions/FormAction.class.php');

class SetMembership extends FormAction
{
  protected function _defineDataspaceName()
  {
    return 'set_membership';
  }

  protected function _initDataspace($request)
  {
    $datasource = Limb :: toolkit()->getDatasource('RequestedObjectDatasource');
    $datasource->setRequest($request);

    $object_data = $datasource->fetch();

    $object = Limb :: toolkit()->createSiteObject('UserObject');

    $data['membership'] = $object->getMembership($object_data['id']);

    $this->dataspace->import($data);
  }

  protected function _validPerform($request, $response)
  {
    $datasource = Limb :: toolkit()->getDatasource('RequestedObjectDatasource');
    $datasource->setRequest($request);

    $object_data = $datasource->fetch();

    $data = $this->dataspace->export();
    $object = Limb :: toolkit()->createSiteObject('UserObject');

    $object->saveMembership($object_data['id'], $data['membership']);

    $request->setStatus(Request :: STATUS_FORM_SUBMITTED);
  }

}

?>