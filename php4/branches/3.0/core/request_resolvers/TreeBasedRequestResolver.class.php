<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: Service.class.php 1191 2005-03-25 14:04:13Z seregalimb $
*
***********************************************************************************/
class TreeBasedRequestResolver // implements ServiceTranslator
{
  function & getRequestedService(&$request)
  {
    if(!$id = $this->_getId($request))
      return new Service('404');

    $toolkit =& Limb :: toolkit();

    $conn =& $toolkit->getDbConnection();
    $sql = 'SELECT sys_service.name FROM sys_object, sys_object_to_service, sys_service
            WHERE sys_object.oid = sys_object_to_service.oid
            AND sys_service.id = sys_object_to_service.service_id
            AND sys_object.oid = :id:';

    $stmt =& $conn->newStatement($sql);
    $stmt->setInteger('id', $id);

    if(!$class_name = $stmt->getOneValue())
      return new Service('404');

    return new Service($class_name);
  }

  function getRequestedAction(&$request)
  {
    if($action = $request->get('action'))
      return $action;

    return false;
  }

  function & getRequestedEntity(&$request)
  {
    if(!$id = $this->_getId($request))
      return false;

    $toolkit =& Limb :: toolkit();

    $conn =& $toolkit->getDbConnection();
    $sql = 'SELECT sys_class.name FROM sys_object, sys_class
            WHERE sys_object.class_id = sys_class.id
            AND sys_object.oid = :id:';

    $stmt =& $conn->newStatement($sql);
    $stmt->setInteger('id', $id);

    if(!$class_name = $stmt->getOneValue())
      return false;

    $uow =& $toolkit->getUOW();
    return $uow->load($class_name, $id);
  }

  function _getId(&$request)
  {
    $toolkit =& Limb :: toolkit();
    $uri =& $request->getUri();
    $path = $uri->getPath();

    $path2id_translator =& $toolkit->getPath2IdTranslator();

    return $path2id_translator->toId($path);
  }
}

?>