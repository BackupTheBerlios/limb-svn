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

class EditSimpleObjectCommand
{
  var $map;
  var $object_handle;

  function EditSimpleObjectCommand($map, &$object_handle)
  {
    $this->map = $map;
    $this->object_handle =& $object_handle;
  }

  function perform()
  {
    if(!$object =& $this->_findObjectInUnitOfWork())
      return LIMB_STATUS_ERROR;

    $this->_populateObjectUsingDataspace($object);

    return LIMB_STATUS_OK;
  }

  function &_findObjectInUnitOfWork()
  {
    $object =& Handle :: resolve($this->object_handle);

    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $uow =& $toolkit->getUOW();
    return $uow->load($object->__class_name, $request->get('id'));
  }

  function _populateObjectUsingDataspace(&$object)
  {
    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->getDataspace();

    foreach($this->map as $key => $setter)
    {
      if (($value = $dataspace->get($key)) !== false)
        $object->set($setter, $value);
    }
  }
}

?>
