<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: DAO.class.php 1159 2005-03-14 10:10:35Z pachanga $
*
***********************************************************************************/

class RequestedObjectDAO
{
  function RequestedObjectDAO(){}

  function & fetch()
  {
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();

    if(!$id = $request->get('id'))
      return new Dataspace();

    $conn =& $toolkit->getDBConnection();

    $sql = 'SELECT sys_class.name FROM sys_object, sys_class WHERE sys_object.class_id = sys_class.id ' .
                                                                    'AND sys_object.oid =' . $id;
    $stmt =& $conn->newStatement($sql);
    $class_name = $stmt->getOneValue();

    $uow =& $toolkit->getUOW();

    if(!$object = $uow->load($class_name, $id))
      return new Dataspace();

    return $object;
  }
}

?>
