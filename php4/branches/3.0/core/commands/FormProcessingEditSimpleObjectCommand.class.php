<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: FormProcessingCommand.class.php 1143 2005-03-05 11:04:06Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/commands/FormProcessingCommand.class.php');

class FormProcessingEditSimpleObjectCommand extends FormProcessingCommand
{
  function _initializeDataspace(&$dataspace)
  {
    if (!$object =& $this->_findObjectInUnitOfWork())
      return LIMB_STATUS_ERROR;

    foreach($this->_defineObject2DataspaceMap() as $getter => $key)
      $dataspace->set($key, $object->$getter());

    parent :: _initializeDataspace($dataspace);
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
