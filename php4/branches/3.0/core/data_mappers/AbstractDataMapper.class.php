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
require_once(LIMB_DIR . '/core/util/ComplexArray.class.php');

class AbstractDataMapper
{
  function getIdentityKeyName()
  {
    return 'id';
  }

  function defineDataMap(){}

  function load(&$record, &$object){}

  function save(&$object)
  {
    $toolkit =& Limb :: toolkit();
    $uow =& $toolkit->getUOW();

    if($uow->isNew($object))
    {
      $this->insert($object);
    }
    elseif($uow->isDirty($object))
    {
      $this->update($object);
    }
  }

  //protected
  function insert(&$object){}

  //protected
  function update(&$object){}

  function delete(&$object){}
}

?>
