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

    $dao =& $toolkit->createDAO('ObjectsClassNamesDAO');
    if(!$dataspace = $dao->fetchById($id))
      return new Dataspace();

    $uow =& $toolkit->getUOW();

    if(!$object = $uow->load($dataspace->get('name'), $id))
      return new Dataspace();

    return $object;
  }
}

?>
