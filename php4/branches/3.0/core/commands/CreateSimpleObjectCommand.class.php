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
  function perform()
  {
    $object =& Handle :: resolve($this->_defineObjectHandle());

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

    foreach($this->_defineDataspace2ObjectMap() as $key => $setter)
    {
      if (($value = $dataspace->get($key)) !== false)
        $object->set($setter, $value);
    }
  }

  function _defineDataspace2ObjectMap()
  {
    return array();
  }

  function &_defineObjectHandle()
  {
    return false;
  }
}

?>
