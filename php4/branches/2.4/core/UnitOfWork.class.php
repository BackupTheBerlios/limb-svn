<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: DomainObject.class.php 1028 2005-01-18 11:06:55Z pachanga $
*
***********************************************************************************/
class UnitOfWork
{
  function & _getDAO($class)
  {
    $toolkit =& Limb :: toolkit();
    return $toolkit->createDAO($class . 'DAO');
  }

  function & _getDataMapper($class)
  {
    $toolkit =& Limb :: toolkit();
    return $toolkit->createDataMapper($class . 'Mapper');
  }

  function & _getObject($class)
  {
    $toolkit =& Limb :: toolkit();
    return $toolkit->createObject($class);
  }

  function & load($class, $id)
  {
    $dao =& $this->_getDAO($class);

    $obj =& $this->_getObject($class);

    if(!$record =& $dao->fetchById((int)$id))
      return null;

    $mapper =& $this->_getDataMapper($class);
    $mapper->load($record, $obj);

    return $obj;
  }
}

?>