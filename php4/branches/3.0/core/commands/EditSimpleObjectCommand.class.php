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
  function perform()
  {
    if(!$object =& $this->_findObjectInUnitOfWork())
      return LIMB_STATUS_ERROR;

    $this->_populateObjectUsingDataspace($object);

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
