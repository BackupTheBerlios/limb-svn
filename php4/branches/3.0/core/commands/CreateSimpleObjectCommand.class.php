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

class CreateSimpleObjectCommand
{
  var $map = array();
  var $object_handle = array();

  function CreateSimpleObjectCommand($map, &$handle)
  {
    $this->map = $map;
    $this->object_handle =& $handle;
  }

  function perform()
  {
    $object =& Handle :: resolve($this->object_handle);

    $this->_populateObjectUsingDataspace($object);

    $this->_registerObjectInUnitOfWork($object);

    return LIMB_STATUS_OK;
  }

  function _registerObjectInUnitOfWork(&$object)
  {
    $toolkit =& Limb :: toolkit();
    $uow =& $toolkit->getUOW();

    $uow->register($object);
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
