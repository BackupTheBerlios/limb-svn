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

class ServiceNodeRequestResolver
{
  function & resolve(&$request)
  {
    if(!$id = $request->get('id'))
      return null;

    $toolkit =& Limb :: toolkit();
    $cache =& $toolkit->getCache();

    if($entity =& $cache->get($id, 'service_node_locator'))
      return $entity;

    $conn =& $toolkit->getDbConnection();
    $sql = 'SELECT sys_class.name FROM sys_object, sys_class
            WHERE sys_object.class_id = sys_class.id
            AND sys_object.oid = :id:';

    $stmt =& $conn->newStatement($sql);
    $stmt->setInteger('id', $id);

    if(!$class_name = $stmt->getOneValue())
      return null;

    $uow =& $toolkit->getUOW();
    $entity =& $uow->load($class_name, $id);
    if(!$service =& $entity->getPart('service'))
      return null;

    if(!$node =& $entity->getPart('node'))
      return null;

    $cache->put($id, $entity, 'service_node_locator');

    return $entity;
  }

}

?>
