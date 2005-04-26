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
class TreeBasedServiceRequestResolver
{
  function & resolve(&$request)
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

  function _getId(&$request)
  {
    if($request->hasAttribute('oid'))
      return (int)$request->get('oid');

    $toolkit =& Limb :: toolkit();
    $uri =& $request->getUri();
    $path = $uri->getPath();

    $path2id_translator =& $toolkit->getPath2IdTranslator();

    return $path2id_translator->toId($path);
  }
}

?>