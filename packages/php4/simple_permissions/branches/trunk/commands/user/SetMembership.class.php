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
  function _defineDataspaceName()
  {
    return 'set_membership';
  }

  function _initDataspace(&$request)
  {
    $toolkit =& Limb :: toolkit();
    $datasource =& $toolkit->getDatasource('RequestedObjectDatasource');
    $datasource->setRequest($request);

    $object_data = $datasource->fetch();

    $object =& $toolkit->createSiteObject('UserObject');

    $data['membership'] = $object->getMembership($object_data['id']);

    $this->dataspace->import($data);
  }

  function _validPerform(&$request, &$response)
  {
    $toolkit =& Limb :: toolkit();
    $datasource =& $toolkit->getDatasource('RequestedObjectDatasource');
    $datasource->setRequest($request);

    $object_data = $datasource->fetch();

    $data = $this->dataspace->export();
    $object =& $toolkit->createSiteObject('UserObject');

    $object->saveMembership($object_data['id'], $data['membership']);

    $request->setStatus(Request :: STATUS_FORM_SUBMITTED);
  }

}

?>