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
class InitializeDataspaceFromSimpleObjectCommand
{
  function perform()
  {
    if (!$object =& $this->_findObjectInUnitOfWork())
      return LIMB_STATUS_ERROR;

    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->getDataspace();

    foreach($this->_defineObject2DataspaceMap() as $getter => $key)
      $dataspace->set($key, $object->get($getter));

    return LIMB_STATUS_OK;
  }

  function &_findObjectInUnitOfWork()
  {
    $object =& Handle :: resolve($this->_defineObjectHandle());

    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $uow =& $toolkit->getUOW();
    return $uow->load($object->__class_name, $request->get('id'));
  }

  function _defineObject2DataspaceMap()
  {
    return array();
  }

  function &_defineObjectHandle()
  {
    return false;
  }
}


?>
