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
require_once(dirname(__FILE__) . '/../MetadataManager.class.php');

class SetMetadataAction extends FormAction
{
  function _defineDataspaceName()
  {
    return 'set_metadata';
  }

  function _initDataspace(&$request)
  {
    $toolkit =& Limb :: toolkit();

    $datasource =& $toolkit->getDatasource('RequestedObjectDatasource');
    $datasource->setRequest($request);

    $object_data = $datasource->fetch();

    $data = MetadataManager :: getMetadata($object_data['id']);
    $this->dataspace->import($data);
  }

  function _validPerform(&$request, &$response)
  {
    $toolkit =& Limb :: toolkit();

    $datasource =& $toolkit->getDatasource('RequestedObjectDatasource');
    $datasource->setRequest($request);

    $object_data = $datasource->fetch();

    MetadataManager :: saveMetadata($object_data['id'],
                                      $this->dataspace->get('keywords'),
                                      $this->dataspace->get('description'));

    $request->setStatus(Request :: STATUS_FORM_SUBMITTED);
  }
}
?>